<?php

function pagesToShow( $current_page, $total_pages_count, $links_to_show = 10  ) {
    $result = array();
    $current_page = (int) $current_page;
    
    $start = 0;
    $stop = 0;
    if ($links_to_show % 2 == 0) $links_to_show -= 1;

    
    if ($total_pages_count <= $links_to_show) {
        //echo "$total_pages_count < $links_to_show";
        $start = 1;
        $stop = $total_pages_count;
    } else {
        //echo "$total_pages_count > $links_to_show";
        $start = $current_page - $links_to_show / 2;
        $stop = $current_page + $links_to_show / 2;
        if ($start < 0) $start = 1;
        if ($stop > $total_pages_count) $stop = $total_pages_count;        
    }
    
    for ( $i = $start; $i <= $stop; $i++) {
        $result[] = $i;
    }
    
    return $result;
        
}

?>