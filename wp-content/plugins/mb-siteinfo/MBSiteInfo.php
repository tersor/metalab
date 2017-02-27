<?php

  class MBSiteInfo  {
    public $CurrentThemes;
    public $Themes;
    public $ThemeUpdates;
    public $Plugins;
    public $PluginUpdates;
    public $WPVersion;

    public $Export;

    function __construct( $blog_id=null ){
      if ( $blog_id ){
        switch_to_blog($blog_id);

        $bloginfo = $this->getSubSiteInfo($blog_id);

        // _log($bloginfo);
        $this->Export['blog_id'] = $blog_id;
        $this->Export['blog_name']      = get_bloginfo('name');
        $this->Export['domain']         = $bloginfo->domain;
        $this->Export['path']           = $bloginfo->path;
        $this->Export['registered_at']  = $bloginfo->registered;
        $this->Export['last_updated']   = $bloginfo->last_updated;
        $this->Export['language']       = get_bloginfo('language' );

        if ( $blog_id == 1 ){
          if ( $blogs = $this->getAllSubSites($blog_id) ){
            foreach ($blogs as $key => $Blog) {
              $Blog = new MBSiteInfo($Blog->blog_id);
              $this->Export['subsites'][] = $Blog->Export;
            }
          }
        }
      }

      $this->setWPVersion();
      $this->setThemes();
      $this->setPlugins();

      //_log($this->Export);
    }


    function getSubSiteInfo( $blog_id ){
      global $wpdb;
      $sql = "select * from %sblogs where blog_id = %s";
      $sql = sprintf($sql, $wpdb->base_prefix, $blog_id);
      // _log($wpdb);
      // _log($sql);
      return $wpdb->get_row($sql);
    }


    function getAllSubSites( $blog_id ){
      global $wpdb;
      $sql = "select * from %sblogs where blog_id != %s";
      $sql = sprintf($sql, $wpdb->base_prefix, $blog_id);
      // _log($wpdb);
      _log($sql);
      return $wpdb->get_results($sql);
    }


    function setWPVersion(){
      $this->WPVersion = get_bloginfo('version' );

      if ( $this->WPVersion ){
        $this->Export['wp_version'] = $this->WPVersion;
      }
    }


    function setThemes(){
      $this->getInstalledThemes();
      $this->CurrentTheme = wp_get_theme();

      if ( is_array($this->Themes) ){
        foreach ($this->Themes as $slug => $Theme) {
          // _log($slug);
          $this->Export['themes'][] =
            array(
              'name' => $Theme->get('Name'),
              'active' =>  ( $this->CurrentTheme->get('Name') == $Theme->get('Name') ) ? true : false,
              'update' => $this->hasUpdate($slug, $this->ThemeUpdates),
              'version' => $Theme->get('Version')
            );
        }
      }
    }


    function getInstalledThemes(){
      global $blog_id;
      $current_blog_id = $blog_id;

      if ( is_multisite() ){
        switch_to_blog(1);
      }

      $this->Themes = wp_get_themes();
      $this->ThemeUpdates = get_option('_site_transient_update_themes');

      if ( is_multisite() ){
        switch_to_blog($current_blog_id);
      }


    }



    function getInstalledPlugins(){
      global $blog_id;
      $current_blog_id = $blog_id;

      if ( is_multisite() ){
        switch_to_blog(1);
      }

      $this->Plugins = get_plugins();
      $this->PluginUpdates = get_option('_site_transient_update_plugins');

      if ( is_multisite() ){
        switch_to_blog($current_blog_id);
      }

    }



    function setPlugins(){
      if ( ! function_exists( 'get_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
      }

      $this->getInstalledPlugins();

        foreach ($this->Plugins as $slug => $plugin) {
        $this->Export['plugins'][] =
          array(
            'name' => $plugin['Name'],
            'active' => ( is_plugin_active($slug) ) ? true : false,
            'update' => $this->hasUpdate($slug, $this->PluginUpdates),
            'version' => $plugin['Version']
          );
      }
    }


    function hasUpdate( $slug, $updates ){
      $has_update = false;
      // _log($updates);
      if ( isset($updates->response)
        && isset($updates->response[$slug]) ){

        if ( isset($updates->response[$slug]->new_version) && trim($updates->response[$slug]->new_version) ){
          $has_update = true;
        }
        else if ( is_array($updates->response[$slug]) && isset($updates->response[$slug]['new_version']) ){
          $has_update = true;
        }
      }



      return $has_update;
    }




}