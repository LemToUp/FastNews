jQuery(document).ready(function(){
    count = 0;
    
    jQuery("#fastnews a").click(function(e){
        e.preventDefault();
        jQuery("#fastnews").hide("slow");
    });

    jQuery( document ).on( "fnGetEvent", {}, 
        function() {
        count ++;
        jQuery('.fn_count p').html(count);  
    });
});