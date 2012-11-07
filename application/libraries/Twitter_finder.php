<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *  Crawls URLs and searches for the twitter account that is likely
 *  associated with the domain
 *
 *  Library is dependent on Phil Suturgeons CURL CI library
 *  Loaded in construct.
 *
 * @author          James Blackwell
 */

class Twitter_finder {

    protected $ci;
    //CI instance
    protected $url;
    //url to scrape
    protected $html;
    //html of searched page
    protected $dom;
    //DOM representation
    public $results;
    //array of results

    function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->library('curl');
    }

    //------------------------------------------------------------------------------

    /**
     * Call this for simple access, this runs the methods returing when
     * it's found a likely match for speed.
     *
     * @author James Blackwell
     * @return array
     */
    public function fetch($url)
    {
        $this->results = array();

        //first we scrape the page provided
        if ($this->create($url))
        {
            //look for near certain matches, this keeps things quick
            if ($this->find_twitter_accounts())
                return $this->results;

            //no luck with searching the page provided, try to find a contact page
            //note done yet :)
            return FALSE;
        }
        
        return FALSE;
    }

    //------------------------------------------------------------------------------

    /**
     * Fetches the page and creates a html variable,
     * then tries to create a dom object if possible
     *
     * @param string
     * @param string
     * @author James Blackwell
     * @return bool
     */
    private function create($url)
    {
        //ensure http is there
        $this->url = prep_url(trim($url));

        $this->ci->curl->create($this->url);

        $this->ci->curl->option(CURLOPT_RETURNTRANSFER, true);
        $this->ci->curl->option(CURLOPT_ENCODING, 'gzip');
        $this->ci->curl->option(CURLOPT_TIMEOUT, 30);
        $this->ci->curl->option(CURLOPT_HEADER, 0);
        $this->ci->curl->option(CURLOPT_FOLLOWLOCATION, 1);
        $this->ci->curl->option(CURLOPT_MAXREDIRS, 5);
        $this->ci->curl->option(CURLOPT_SSL_VERIFYPEER, FALSE);
        $this->ci->curl->option(CURLOPT_SSL_VERIFYHOST, FALSE);

        $this->html = $this->ci->curl->execute();

        if ($this->html)
        {
            $this->dom = new DOMDocument();
            //suppress errors for bad html
            @$this->dom->loadHTML($this->html);
            return TRUE;
        }
        return FALSE;
    }

    //------------------------------------------------------------------------------

    /**
     * Look through the dom and try and find any twitter accounts.
     * This currently works by finding all links, then filtering out links to
     * twitter.com, excluding api.twitter.com in the process.
     *
     * @author James Blackwell
     * @return bool
     */
    private function find_twitter_accounts()
    {
        if ( ! $this->dom)
            return FALSE;

        foreach ($this->dom->getElementsByTagName("a") as $a)
        {
            $href = trim($a->getAttribute("href"));

            if ( ! $href OR ! $this->is_valid_twitter_account_url($href))
                continue;

            //at this point, we should have a twitter.com/** URL left
            //which is quite likely to be an account URL
            //first we're looking if there is a twitter URL with a
            //username matching the domain
            
            //see if it's a "follow us" type link
            if ($this->is_anchor_follow_link($a->nodeValue))
            {
                //it is, extract the username
                $twitter_account = $this->extract_username_from_url($href);
                
                $this->results = array(
                    'find_method' => 'on_page_link',
                    'twitter_account' => $twitter_account,
                    'raw_anchor_text' => $a->nodeValue,
                    'raw_url_found' => $href
                );
                return TRUE;
            }

            //strip the url for the hostname - tld
            $domain = $this->get_host_no_tld();

            //if it matches the domain (stripped of tld) return
            if ($this->search_link_for_username($href, $domain))
            {
                //result should be the parsed domain name which == username
                $this->results = array(
                    'find_method' => 'matching_domain',
                    'twitter_account' => $domain
                );

                //results
                return TRUE;
            }

            
        }
        return FALSE;
    }

    //------------------------------------------------------------------------------

    /**
     * Parse the url and return the twitter username
     * Pretty simple approach, utilises existing functions to clean url
     * plus some extra to get rid of some common twitter stuff
     * then splits the url by remaining
     *
     */
    public function extract_username_from_url($href)
    {
        //remove http and www
        $href = remove_www(remove_http($href));
        $href = str_replace(array('twitter.com', '/lists', '/memberships', '#!/'), '', $href);
        $href = rtrim(ltrim($href, '/'), '/');
        //at this point we should have a single username hopefully.
        //we'll check for any forward slashes and return the first segment if so
        if (strstr($href, '/'))
        {
            $href = explode('/', $href);
            return $href[0];
        }
        
        return $href;
    }
    
    //------------------------------------------------------------------------------

    /**
     * Looks in the twitter link found for a username
     * Uses the slashes to check it's in the right place
     *
     * @author James Blackwell
     * @param string
     * @param string
     */
    public function search_link_for_username($href, $username)
    {
        if (stristr($href, $username . "/") OR stristr($href, "twitter.com/" . $username))
            return TRUE;
    }

    //------------------------------------------------------------------------------

    /**
     * Quick function to get the domain, returns first subdomain
     *
     * @author James Blackwell
     * @return string
     */
    public function get_host_no_tld()
    {
        $domain = get_domain_name($this->url);
        $domains = explode('.', $domain);
        return $domains[0];
    }

    //------------------------------------------------------------------------------

    /**
     * Tests if the anchor text contains some common "follow us" type strings
     *
     * @param string
     * @return bool
     * @author James Blackwell
     */
    private function is_anchor_follow_link($anchor)
    {
        $needles = array(
            'follow us',
            'follow me',
            'follow @'
        );

        foreach ($needles as $needle)
        {
            //note the case insensitive version
            if (stristr($anchor, $needle))
                return TRUE;
        }

        //if it matches twitter exactly, thats ok for now I think
        if (trim(strtolower($anchor)) == 'twitter')
            return TRUE;

        return FALSE;
    }

    //------------------------------------------------------------------------------

    /**
     * Checks if the URL is a valid twitter account URL and not some other junk
     * Can definitely improve the regex but this works pretty well for now
     * Note there are a few diff types of twitter urls that are valid,
     * mainly thanks to the hashbang syntax they used
     *
     * @author James Blackwell
     * @param string
     * @return bool
     */
    private function is_valid_twitter_account_url($url)
    {
        //match twitter.com/ with at least two characters after backslash
        //also exclude some subdomains and share subfolder
        return preg_match('%(?<!api\.)(?<!platform\.)(?<!engineering\.)twitter\.com/.{2,}(?!share/)%', $url);
    }

    //------------------------------------------------------------------------------

}

/* End of file Link_crawler.php */
/* Location: ./application/libraries/Link_crawler.php */
