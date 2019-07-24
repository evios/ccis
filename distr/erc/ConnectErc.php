<?php
require_once "phpQuery/phpQuery/phpQuery.php";

class ConnectErc{
    var $curl;
    var $dir;

    public function ConnectErc($cookieFileDir){
	$this->dir = $cookieFileDir;
    }

    private function init(){
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl ,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; rv:1.9.2.12) Gecko/20101026 AlexaToolbar/alxf-1.54 Firefox/3.6.12 ( .NET4.0E)");
        curl_setopt($this->curl ,CURLOPT_COOKIEJAR, $this->dir."coo");
        curl_setopt($this->curl ,CURLOPT_COOKIEFILE,$this->dir."coo");
        curl_setopt($this->curl ,CURLOPT_REFERER,"https://connect.erc.ua/consumer/");
	curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
        return $this;
    }

    public function authorize($login,$password){

        $this->init();
        curl_setopt($this->curl ,CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($this->curl ,CURLOPT_URL,'https://connect.erc.ua/Logon.aspx?ReturnUrl=%2fdefault.aspx&AspxAutoDetectCookieSupport=1');
        curl_setopt($this->curl ,CURLOPT_POST,FALSE);
        $page_data = curl_exec($this->curl);
        $code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        if($code!=200)
            die("Can't get main page, code $code");

	phpQuery::newDocument($page_data);
	$post_data = array(
	    "__EVENTARGUMENT" => "",
	    "__EVENTTARGET" => "btLogin",
	    "__EVENTVALIDATION" => pq("#__EVENTVALIDATION")->val(),
	    "__LASTFOCUS" => "",
	    "__VIEWSTATE" => pq("#__VIEWSTATE")->val(),
	    "edEmail" => $login,
	    "edPassword" => $password,
	    "hidBlockedPopup" => "0",
	    "hidClientScreenHeight" => "1080",
	    "hidClientScreenWidth" => "1920"
	);

        curl_setopt($this->curl ,CURLOPT_POST,TRUE);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post_data);
        $page_data = curl_exec($this->curl);
        $code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        if($code!=200)
            die("Can't login, code $code");

        return $this;
    }

    public function btexportprice(){

	global $local_cvs_file;
        $this->init();
        curl_setopt($this->curl ,CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($this->curl ,CURLOPT_URL,'https://connect.erc.ua/consumer/default.aspx');
        curl_setopt($this->curl ,CURLOPT_POST,FALSE);
        $page_data = curl_exec($this->curl);
        $code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        if($code!=200)
            die("Can't get main page, code $code");

	phpQuery::newDocument($page_data);
	$post_data = array(
	    "__EVENTARGUMENT" => "",
	    "__EVENTTARGET" => "ctl00\$cM\$cPriceExport\$btExport",
	    "__LASTFOCUS" => "",
	    "ctl00_a_smMain_HiddenField" => "",
	    "ctl00_cM_cVM_tvV_SelectedNode" => "",
	    "ctl00_cM_cVM_tvV_PopulateLog" => "",
	    "__VIEWSTATEENCRYPTED" => "",
	    "ctl00\$a_smMain" => "",
	    "ctl00\$cM\$cPriceExport\$liVendors" => "0",
	    "ctl00\$cM\$cPriceExport\$liCategory" => "0",
	    "ctl00\$cM\$cPriceExport\$rbExportType" => "csv",
	    "ctl00\$cM\$cPriceExport\$chkZipOutput" => "",
	    "ctl00\$cM\$cPriceExport\$chkDesc" => "",
	    "ctl00\$cM\$cPriceExport\$chkActions" => "",
	    "ctl00\$cM\$cVM\$hidVndId" => "",
	    "ctl00\$cM\$cVM\$hidCatId" => "",
	    "ctl00\$cM\$cVM\$hidSubCatId" => "",
	    "ctl00\$cM\$cVM$\btSetParams" => "",
	    "ctl00_cM_cVM_tvV_ExpandState" => "ccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccc",
	    "__VIEWSTATE" => pq("#__VIEWSTATE")->val(),
	    "hidBlockedPopup" => "0",
	    "hidClientScreenHeight" => "1080",
	    "hidClientScreenWidth" => "1920"
	);

        curl_setopt($this->curl ,CURLOPT_POST,TRUE);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post_data);
        $page_data = curl_exec($this->curl);
        $code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        if($code!=200)
            die("Can't login, code $code");
	file_put_contents($local_cvs_file, $page_data);
        return $this;
    }
}
?>
