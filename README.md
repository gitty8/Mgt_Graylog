### Welcome to the Mgt Graylog Extension for Magento!

We know it cost you a lot time to delete the daily received spam through the contact and product review form.
With our magento Akismet (Automattic Kismet (Akismet for short)) extension for magento no installation of CAPCTHA is needed. 
A CAPCTHA is not very user friendly and statics have shown that it can be cracked. 
Also you must not install something difficult on your magento frontend like a CAPCTHA.
Here our "Akismet spam prevention for magento" is the best solution to kill the spam. 

## MORE INFORMATION

[http://www.mgt-commerce.com/blog/how-to-manage-your-magento-logs-with-graylog2/](http://www.mgt-commerce.com/blog/how-to-manage-your-magento-logs-with-graylog2/)

## INSTALLATION

* copy all files to your magento installation
* Open app/Mage.php and add the following line

<pre><code>
  public static function run($code = '', $type = 'store', $options = array())
  {
        ......
    } catch (Exception $e) {
    if (self::isInstalled() || self::$_isDownloader) {
    //add this line
    self::dispatchEvent('mage_run_exception',array('exception' => $e));
    //-----------------------------------------------------------------
    self::printException($e);
    exit();
    }
  }
</code></pre>

* Clear the cache in Admin -> System -> Cache Management 
* Login/Logout from Backend to reload the ACL
* Go to Admin -> System -> Configuration -> MGT-COMMERCE.COM -> Graylog -> Settings -> Active -> Yes
* Enter your Graylog2 host
* Have fun and give Feedback

## CHANGELOG

1.0.1

* Add installation file

1.0.0

* Initial release