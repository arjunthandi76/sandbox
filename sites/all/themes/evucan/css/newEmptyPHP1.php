<?php


if ($county_id !== 0) {
  $countyHandler = xoops_getmodulehandler('county', 'extcal');
  $county = $countyHandler->objectToArray($countyHandler->getCountybySlug($countyslug)); 
  $county ['link_path'] = makeLocWhatsOnLinkPath ($county ['county_slug'] ) ;
  $county ['events_link_path'] = makeLocEventsLinkPath ($county ['county_slug']) ;
  $xoopsTpl->assign('county', $county);
}
if ($town_id !== 0) {
  $townHandler = xoops_getmodulehandler('town', 'extcal');
  $town= $townHandler->objectToArray($townHandler->getTownbySlug($townslug)); 
  $town['link_path'] = makeLocWhatsOnLinkPath ("", $town['town_slug']) ;
  $town['events_link_path'] = makeLocEventsLinkPath ($county['county_slug'], $town['town_slug']) ;
  $xoopsTpl->assign('town', $town);
}
if ($venue_id !== 0) {
  $venueHandler = xoops_getmodulehandler('venue', 'extcal');
  $venue = $venueHandler->objectToArray($venueHandler->getVenuebySlug($town['town_id'])); 
  $venue['events_link_path'] = makeLocEventsLinkPath ($county['county_slug'], $town['town_slug'], $venue['venue_slug']) ;
  $xoopsTpl->assign('venue', $venue);
}

?>
