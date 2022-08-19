<?php

/*
 * @Classe para conversasao Facebook
 * @PARAM:
*/
class ConversaoPixelFacebook
{
    function __set( $atrib, $valor )
    {
        $this->$atrib = $valor ? $valor : null;
    }

    function __get($atrib)
    {
        return $this->$atrib;
    }

    /*
     * @Funcao: codigo_viws_html
     * @PARAM: paginas_solicitacao
    */
    function codigo_viws_html()
    {
        $this->html = '';
        switch( $this->paginas_solicitacao ) :

            case 'pesquisar' :
                // Search
                // Track searches on your website (ex. product searches)
                $this->html = "fbq('track', 'Search');";
            break;
            case 'carrinho' :
                // Search
                // Track searches on your website (ex. product searches)
                $this->html = "fbq('track', 'AddToCart');";
            break;

            case 'carrinho' :
                // AddToCart
                // Track when items are added to a shopping cart (ex. click/landing page on Add to Cart button)
                $this->html = "fbq('track', 'AddToCart');";
            break;

            case 'lista-desejos':
                // AddToWishlist
                // Track when items are added to a wishlist (ex. click/landing page on Add to Wishlist button)
                $this->html = "fbq('track', 'AddToWishlist');";
            break;

            case 'login' :
                // InitiateCheckout
                // Track when people enter the checkout flow (ex. click/landing page on checkout button)
                $this->html = "fbq('track', 'InitiateCheckout');";
            break;

            case 'minha-compra':
                // AddPaymentInfo
                // Track when payment information is added in the checkout flow (ex. click/landing page on billing info)
                $this->html = "fbq('track', 'AddPaymentInfo');";
            break;

            case 'finalizacao-pagamento':
                // Purchase
                // Track purchases or checkout flow completions (ex. landing on "Thank You" or confirmation page)
                $this->html = "fbq('track', 'Purchase', {value: '1.00', currency: 'USD'});";
            break;

            case 'anuncio':
                // Lead
                // Track when a user expresses interest in your offering (ex. form submission, sign up for trial, landing on pricing page)
                $this->html = "fbq('track', 'Lead');";
            break;

            case 'usuarionovo' :
                // CompleteRegistration
                // Track when a registration form is completed (ex. complete subscription, sign up for a service)
                $this->html = "fbq('track', 'CompleteRegistration');";
            break;

            default :
                // ViewContent
                // Track key page views (ex: product page, landing page or article)
                $this->html = "fbq('track', 'ViewContent');";
            break;

        endswitch;

        return $this->html;
    }

    /*
     * @Funcao: insere_codigo_html 
     * @INSERE O CODIGO DO FACEBOOK NA PAGINA HTML
     * @PARAM: codigo_usuario 
     * @codigo_usuario SERÁ O CODIGO GERADO NA CONVERSAO PIXEL FACEBOOK
    */
    function insere_codigo_html()
    {
        $this->html = ''
        . "<!-- Facebook Pixel Code -->\n"
        . "\t\t<script>\n"
        . "\t\t\t!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?\n"
        . "\t\t\tn.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;\n"
        . "\t\t\tn.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;\n"
        . "\t\t\tt.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,\n"
        . "\t\t\tdocument,'script','//connect.facebook.net/pt_BR/fbevents.js');\n"

        . "\t\t\tfbq('init', '{$this->codigo_usuario}');\n"
        . "\t\t\tfbq('track', 'PageView');\n"

        . "\t\t\t" . $this->codigo_viws_html()

        . "\n\t\t"
        . "</script>\n"

        . "\t\t<noscript><img height='1' width='1' style='display:none' src='https://www.facebook.com/tr?id={$this->codigo_usuario}&ev=PageView&noscript=1'/></noscript>\n"
        . "\t\t<!-- End Facebook Pixel Code -->\n";



        return $this->html;
    }
}