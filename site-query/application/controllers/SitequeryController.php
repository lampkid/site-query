<?php
class SitequeryController extends Zend_Controller_Action
{
        /**
         *返回所有种类的信息,或是根据情况转发请求到其他Action
         */
        public function indexAction()
        {
             $querys = $_GET['querys'];
            /* 
             if(!is_array($querys))
             {
             //   $this->hostAction(); 
             //   $this->seoAction($_GET['query-site']);
             //   $this->ipwhoisAction();
                exit;
             }
             */
             
             if(isset($querys) && !empty($querys) && is_array($querys))
             {
                 if(in_array('host-query',$querys))
                 {
                    $this->hostAction(); 
                 }

                 if(in_array('seo-query',$querys))
                 {
                    $this->seoAction($_GET['query-site']);
                 }

                 if(in_array('ipwhois-query',$querys))
                 {
                     $this->ipwhoisAction();
                 }
             } 
        }

        public function hostAction()
        {
             $this->view->host = array(
                                "服务器主机ip" => $_SERVER['SERVER_ADDR'],
                                "服务器" => $_SERVER['SERVER_SOFTWARE'],
                                "网站根目录" => $_SERVER['DOCUMENT_ROOT'],
                                "网站管理员" => $_SERVER['SERVER_ADMIN'],
                                "主机环境变量" => $_SERVER['PATH'],
                                );
        
            
        }

        public function seoAction($site)
        {
            $client = new Zend_Http_Client($site);
            $response = $client->request('GET');

            $html = $response->getBody();

            $dom = new Zend_Dom_Query($html);
           // print "<pre>";
           // var_dump($html);
           // print "</pre>";

            $titles = $dom->query('title'); 
            foreach($titles as $title)
            {
                //print_r($title);
                $this->view->seo = array(
                                    "站点标题" => $title->nodeValue,
                                    );
            }

            $metas = $dom->query('meta');

            $dictionary = array(
                             "keywords" => "关键词",
                             "Keywords" => "关键词",
                             "description" => "网站描述",
                             "author" => "作者",
                             "Copyright" => "版权",
                             );

            foreach($metas as $meta)
            {
                if($meta->hasAttribute('name') && $meta->hasAttribute('content'))
                {
                    $dictionary_key = trim($meta->getAttributeNode('name')->value);
                    $seo_key = $dictionary[$dictionary_key];
                    $seo_value = $meta->getAttributeNode('content')->value;
                    $this->view->seo[$seo_key] = $seo_value;
                }
            }


        }

        public function ipwhoisAction()
        {
            /*
            $domain = $_GET['query-site'];
            $domain = str_replace("http://", "", $domain);
            $client = new Zend_Http_Client("http://www.henan100.com/tool/zhanzhang/whois/?action=do&domain=".$domain);
            $response = $client->request('POST');
                           
            $html = $response->getBody();
            $dom = new DOMDocument("1.0");
            $dom->loadHTML($html);
            $ipwhois->getElementById('result');
            $this->view->ipwhois = $ipwhois;
            */
            $domain = $_GET['query-site'];
            $domain = str_replace("http://", "", $domain);
            $domain = str_replace("www.", "", $domain);
            exec("whois {$domain}",$ipwhois);
            $this->view->ipwhois = $ipwhois;
        }
    
        public function recordAction()
        {

        }

    }
?>
