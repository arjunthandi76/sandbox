<?php


if ($county_id ==0 && $town_id ==0) {  // _GET value from county_id takes precedence over slug (because the user might conduct another search)
    // Check whether if we are just getting all county values or not
    if (CheckIfGetAllCounties($countyslug) == true) {
        $county_id = 0;
    } else {
        // Get specific county by slug
        $countyHandler = xoops_getmodulehandler('county', 'extcal');
        $county = $countyHandler->objectToArray($countyHandler->getCountybySlug($countyslug)); 
    
        if (!empty($county)) {
            $county_id = $county['county_id'];
            //$upcoming_title = $county['county_title'];
    
        }else {
            $county_id = 0;
        }
        
        unset ($county);
    }
    // Check whether if we are just getting all town values or not
    if (CheckIfGetAllTowns($townslug) == true) {
        $town_id = 0;
    } else {
        // Get specific town by slug
        $townHandler = xoops_getmodulehandler('town', 'extcal');
        $town= $townHandler->objectToArray($townHandler->getTownbySlug($townslug)); 
        if (!empty($town)) {
            $town_id = $town['town_id'];
        } else {
            $town_id = 0;
        }
        unset ($town);
    }
    
    if ($venue_id ==0 && $venueslug !== "") {
        // Get specific town by slug
        $venueHandler = xoops_getmodulehandler('town', 'extcal');
        $venue = $venueHandler->objectToArray($venueHandler->getVenuebySlug($venueslug)); 
        if (!empty($venue)) {
            $venue_id = $venue['venue_id'];
        } else {
            $venue_id = 0;
        }
        unset ($venue);    
      
    }
}

?>
