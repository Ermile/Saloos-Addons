<?php
namespace lib\google;
require_once ('libraries/Google/autoload.php');

class google
{

     // self::$social['google']['status']                      = true;
     // self::$social['google']['clinet_id']                   = '395232553225-140n5eq7ha34thagrbcnns125ocmqk96.apps.googleusercontent.com';
     // self::$social['google']['project_id']                  = 'ermile-tejarak';
     // self::$social['google']['auth_uri']                    = 'https://accounts.google.com/o/oauth2/auth';
     // self::$social['google']['token_uri']                   = 'https://accounts.google.com/o/oauth2/token';
     // self::$social['google']['auth_provider_x509_cert_url'] = 'https://www.googleapis.com/oauth2/v1/certs';
     // self::$social['google']['client_secret']               = 'h3q_yNJFqbqO5SV-PK2cXMmc';



     //Insert your cient ID and secret
     //You can get it from : https://console.developers.google.com/
     private static $client_id     = null;
     private static $client_secret = null;
     private static $redirect_url  = null;
     private static $client        = null;
     private static $service       = null;
     private static $authUrl       = null;
     private static $userinfo      = null;

     /**
     * ready to connect to google
     */
     private static function config()
     {
          if(!\lib\option::social('google', 'status'))
          {
               return false;
          }
          /**
           * get client id
           */
          self::$client_id     = \lib\option::social('google', 'client_id');
          /**
           * get client secret
           */
          self::$client_secret = \lib\option::social('google', 'client_secret');

          if(\lib\option::social('google', 'redirect_url'))
          {
               self::$redirect_url  = \lib\option::social('google', 'redirect_url');
          }
          else
          {
               self::$redirect_url  = Protocol. '://';
               self::$redirect_url .= \lib\router::get_domain(1). '.'.  Tld;
               self::$redirect_url .= \lib\define::get_current_language_string();
               self::$redirect_url .= '/enter/google';
          }


          /************************************************
          Make an API request on behalf of a user. In
          this case we need to have a valid OAuth 2.0
          token for the user, so we need to send them
          through a login flow. To do this we need some
          information from our API console project.
          ************************************************/
          self::$client = new \Google_Client();
          self::$client->setClientId(self::$client_id);
          self::$client->setClientSecret(self::$client_secret);
          self::$client->setRedirectUri(self::$redirect_url);
          self::$client->addScope("email");
          self::$client->addScope("profile");

          /************************************************
          When we create the service here, we pass the
          client to it. The client then queries the service
          for the required scopes, and uses that when
          generating the authentication URL later.
          ************************************************/
          self::$service = new \Google_Service_Oauth2(self::$client);

          return true;
     }


     /**
      * return auth url
      */
     public static function auth_url()
     {
          if(!self::config())
          {
               return false;
          }

          self::$authUrl = self::$client->createAuthUrl();
          return self::$authUrl;
     }


     /**
     * check access token
     *
     * @return     boolean  ( description_of_the_return_value )
     */
     public static function check()
     {
          /************************************************
          If we have a code back from the OAuth 2.0 flow,
          we need to exchange that with the authenticate()
          function. We store the resultant access token
          bundle in the session, and redirect to ourself.
          */
          if(\lib\utility::get('code'))
          {
               if(!self::config())
               {
                    return false;
               }
               self::$client->authenticate(\lib\utility::get('code'));

               $access = self::$client->getAccessToken();

               if($access)
               {
                    self::$client->setAccessToken($access);

                    self::$userinfo = self::$service->userinfo->get();

                    return true;
               }
               else
               {
                    \lib\debug::error(T_("Invalid access token"));
                    return false;
               }
          }
          return false;
     }


     /**
      * get user info
      *
      * @return     <type>  ( description_of_the_return_value )
      */
     public static function user_info()
     {
          return self::$userinfo;
     }
}
?>