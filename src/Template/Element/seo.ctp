<?php 

/*
 * SEO 设置
 */
    $seo_key = '';
    if (isset($_request_params['prefix'])&&!empty($_request_params['prefix'])){
        $seo_key.= $_request_params['prefix'].'_';
    }
    $seo_key .= $_request_params['controller'].'_'.$_request_params['action'];
    $old_seo_key = $seo_key;
    if ($_request_params['pass']){
        $seo_key.= '_'.implode('_',$_request_params['pass']);
    }
    //debug($seo_key);
    if (\Cake\Core\Configure::check('Seo.'.$seo_key.'.title')){
        $this->start('title');
        echo \Cake\Core\Configure::read('Seo.'.$seo_key.'.title');
        $this->end();
        
        $this->start('meta');
        echo $this->Html->meta(
                'keywords',
                \Cake\Core\Configure::read('Seo.'.$seo_key.'.keywords')
        );
        echo $this->Html->meta(
                'description',
                 \Cake\Core\Configure::read('Seo.'.$seo_key.'.description')
        );
        $this->end();
    }elseif (\Cake\Core\Configure::check('Seo.'.$old_seo_key.'.title')){
        $this->start('title');
        echo \Cake\Core\Configure::read('Seo.'.$old_seo_key.'.title');
        $this->end();
        
        $this->start('meta');
        echo $this->Html->meta(
            'keywords',
            \Cake\Core\Configure::read('Seo.'.$old_seo_key.'.keywords')
            );
        echo $this->Html->meta(
            'description',
            \Cake\Core\Configure::read('Seo.'.$old_seo_key.'.description')
            );
        $this->end();
    }else{
        $this->start('title');
        echo '';
        $this->end();
        
        $this->start('meta');
        echo $this->Html->meta(
            'keywords',
            ''
            );
        echo $this->Html->meta(
            'description',
           ''
            );
        $this->end();
    }
?>
