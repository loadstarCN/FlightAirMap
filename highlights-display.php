<?php
require('require/class.Connection.php');
require('require/class.Spotter.php');

$title = "Special Highlights";
require('header.php');

//calculuation for the pagination
if($_GET['limit'] == "")
{
  $limit_start = 0;
  $limit_end = 28;
  $absolute_difference = 28;
}  else {
	$limit_explode = explode(",", $_GET['limit']);
	$limit_start = $limit_explode[0];
	$limit_end = $limit_explode[1];
}
$absolute_difference = abs($limit_start - $limit_end);
$limit_next = $limit_end + $absolute_difference;
$limit_previous_1 = $limit_start - $absolute_difference;
$limit_previous_2 = $limit_end - $absolute_difference;

$page_url = $globalURL.'/highlights';

?>
 
  <?php
  
  	print '<div class="info column">';
        print '<div class="view-type">';
            print '<a href="'.$globalURL.'/highlights" class="active" alt="Display View" title="Display View"><i class="fa fa-th"></i></a>';
            print '<a href="'.$globalURL.'/highlights/table" alt="Table View" title="Table View"><i class="fa fa-table"></i></a>';
        print '</div>';
  		print '<h1>Special Highlights</h1>';
  	print '</div>';
  	
  	print '<div class="column">';	
	  	print '<p>The view below shows all aircrafts that have been selected to have some sort of special characteristic about them, such as unique liveries, destinations etc.</p>';
	  
		  $spotter_array = Spotter::getSpotterDataByHighlight($limit_start.",".$absolute_difference, $_GET['sort']);
		
		  if (!empty($spotter_array))
		  {	
				
              print '<div class="dispay-view">';
                foreach($spotter_array as $spotter_item)
                {
                    if ($spotter_item['image'] != "")
                     {
                        print '<div>';
                            print '<a href="'.$globalURL.'/flightid/'.$spotter_item['spotter_id'].'"><img src="'.$spotter_item['image'].'" alt="'.$spotter_item['highlight'].'" title="'.$spotter_item['registration'].' - '.$spotter_item['aircraft_name'].' ('.$spotter_item['airline_name'].')" data-toggle="popover" data-content="'.$spotter_item['highlight'].'" /></a>';
                        print '</div>';
                     } else {
                     print '<div>';
                        print '<a href="'.$globalURL.'/flightid/'.$spotter_item['spotter_id'].'"><img src="'.$globalURL.'/images/placeholder_thumb.png" alt="'.$spotter_item['highlight'].'" title="'.$spotter_item['highlight'].'" data-toggle="popover" data-content="'.$spotter_item['highlight'].'" /></a>';
                     print '</div>';
                     }
                }
              print '</div>';
              
              
			  print '<div class="pagination">';
			  	if ($limit_previous_1 >= 0)
			  	{
			  	print '<a href="'.$page_url.'/'.$limit_previous_1.','.$limit_previous_2.'/'.$_GET['sort'].'">&laquo;Previous Page</a>';
			  	}
			  	if ($spotter_array[0]['query_number_rows'] == $absolute_difference)
			  	{
			  		print '<a href="'.$page_url.'/'.$limit_end.','.$limit_next.'/'.$_GET['sort'].'">Next Page&raquo;</a>';
			  	}
			  print '</div>';
    
    print '</div>';
			
	  }

  ?>

<?php
require('footer.php');
?>