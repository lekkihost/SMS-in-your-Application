Using PHP Technology, we have a sample program. 
However, you can use other technologies (ASP, ASP.net, JAVA, C++, Python, etc) by following same procedure. 
In your application, integrate the PHP programming codes as below by following the flow process.

/*****Server & Port Details********
 *** http://121.241.242.114:8080 **
 *********************************/
 
 /*** Get/Set the Applicable Vars***/

// Sender variable, this can be name or phone number
$sender = mysql_real_escape_string(trim($_POST['sender'])) ;    
 
// Receiver phone number. Ensure country code attached
$receiver = mysql_real_escape_string(trim($_POST['receiver'])) ;

// Message Type. Example: Text, Multimedia    
$message_type = mysql_real_escape_string(trim($_POST['message_type'])) ;   

// The SMS  
$message = mysql_real_escape_string(trim($_POST['message'])) ;   

// Scheduled Time to Deliver if applicable  
$schedule = mysql_real_escape_string(trim($_POST['schedule'])) ;   

// Date and Time of when message was sent  
$datetime = mysql_real_escape_string(trim($_POST['datetime'])) ;    

ini_set("allow_url_fopen",1);

$host = "121.241.242.114";

$port = "80";

$username = "myuser";      // Username that will be given to you when you order for the Bulk SMS. Click here to order one.

$password = "mypass";      // Password that will be given to you when you order for the Bulk SMS. Click here to order one.

$strMessageType = "0";

$msgtype = $strMessageType;

$strDlr = "1";

$dlr = $strDlr;

$strMessage = $message;

$strSender = $sender;

$strMobile = $receiver;

$mobile = $strMobile;


class Sender
{
        var $host;
        var $port;
        var $strUserName; // myuser
        var $strPassword; // mypass
        var $strSender; // $sender
        var $strMessage; // $message
        var $strMobile; // $receiver
        var $strMessageType; // $message_type
        var $strDlr;

        private function sms__unicode($message)
        {
            $hex1='';
            if (function_exists('iconv'))
            {
                 $latin = @iconv('UTF-8', 'ISO-8859-1', $message);
                 if (strcmp($latin, $message))
                 {
                      $arr = unpack('H*hex', @iconv('UTF-8', 'UCS-2BE', $message));
                      $hex1 = strtoupper($arr['hex']);
                 }
                 if($hex1 =='')
                 {
                      $hex2 = '';
                      $hex = '';
                      for ($i = 0; $i < strlen($message); $i++)
                      {
                            $hex = dechex(ord($message[$i]));
                            $len = strlen($hex);
                            $add = 4 - $len;
                            if($len < 4)
                            {
                                    for($j=0;$j<$add;$j++)
                                    {
                                           $hex="0".$hex;
                                    }
                            }
                            $hex2.=$hex;
                      }
                      return $hex2;
                 }
                 else
                {
                    return $hex1;
                }
            }
            else
            {
                  print 'iconv Function Not Existing !';
            }
        }

        //Constructor..
        public function Sender($host,$port,$username,$password,$sender,$message,$mobile,$msgtype,$dlr))
        {
            $this->host=$host;
            $this->port=$port;
            $this->strUserName = $username;
            $this->strPassword = $password;
            $this->strSender= $sender;
            $this->strMessage=$message; //URL Encode The Message..
            $this->strMobile=$mobile;
            $this->strMessageType=$msgtype;
            $this->strDlr=$dlr;
        }

        public function Submit()
        {
            if($this->strMessageType=="2" || $this->strMessageType=="6")
            {
                 //Call The Function Of String To HEX.
                 $this->strMessage = $this->sms__unicode($this->strMessage);
                 try
                 {
                      //Smpp http Url to send sms.
                      $live_url="http://".$this->host.":".$this->port."/bulksms/bulksms?username=".$this->strUserName."&password=".$this->strPassword."&type=".$this->strMessageType."&dlr=".$this->strDlr."&destination=".$this->strMobile."&source=".$this->strSender."&message=".$this->strMessage."";
                      $parse_url=file($live_url);
                 }
                 catch(Exception $e)
                 {
                      echo 'Message:' .$e->getMessage();
                 }
           }
           else
           $this->strMessage=urlencode($this->strMessage);

           try
           {
               //Smpp http Url to send sms.
               $live_url="http://".$this->host.":".$this->port."/bulksms/bulksms?username=".$this->strUserName."&password=".$this->strPassword."&type=".$this->strMessageType."&dlr=".$this->strDlr."&destination=".$this->strMobile."&source=".$this->strSender."&message=".$this->strMessage."";
               $parse_url=file($live_url);

               //This is a direct response
               $directresponse = $parse_url[0];
               $pieces = explode("|", $directresponse);

               if ($pieces[0] == "1701")
               {
                   echo "<h2><font color = \"#000000\">Message Sent.</font></h2><br/>";
               }
               elseif ($pieces[0] == "1702")
               {
                   // Wrong Passwod or Username.
                   echo "<h2><font color = \"#000000\">Server Not Responding. Message Has Not Been Sent.</font></h2><br/>";
               }
               else
               {
                   echo "<h2><font color = \"#000000\">Message Not Sent.</font></h2><br/>";
               }
          }
          catch(Exception $e)
          {
               echo 'Message:' .$e->getMessage();
          }
       }
}


//  Call The Constructor.
$obj = new Sender($host,$port,$username,$password,$strSender,$strMessage,$mobile,$msgtype,"1");
$obj->Submit();
