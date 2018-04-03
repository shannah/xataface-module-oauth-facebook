<?php
class modules_oauth_facebook {
    const GRAPH_URL="https://graph.facebook.com/";
    
    public function __construct() {
        $app = Dataface_Application::getInstance();
        $app->registerEventListener('oauth_fetch_user_data', array($this, 'oauth_fetch_user_data'), false);
        $app->registerEventListener('oauth_extract_user_properties_from_user_data', array($this, 'oauth_extract_user_properties_from_user_data'), false);
    }
    
    
    public function oauth_fetch_user_data($evt) {
        if ($evt->service !== 'facebook') {
            return;
        }
        $mod = Dataface_ModuleTool::getInstance()->loadModule('modules_oauth');
         /*
         Array ( [name] => Steve Hannah [id] => 10101058904559733 ) 
         */
        $res = df_http_post(self::GRAPH_URL."me?fields=name", array('access_token'=>$mod->getOauthToken('facebook')));
        if (!@$res['id']) {
            error_log("Facebook login failed with access token");
            throw new Exception("Failed to get facebook profile for access token");
        }
        $data = $res;
        
        
        
        $evt->out = $data;
        return;
        
    }
    
    public function oauth_extract_user_properties_from_user_data($evt) {
        if ($evt->service !== 'facebook') {
            return;
        }
        
        $evt->out = array(
            'id' => $evt->userData['id'],
            'name' => $evt->userData['name'],
            'username' => strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $evt->userData['name']))
        );
    }
    
            
}

