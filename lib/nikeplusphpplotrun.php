<?

function compute_avg($speed, $value, $max, $count = 4) {
  $nb_val = 0;
  $stack = array();

  for ($i = -$count; $i <= $count; $i++) {
    $indice = $value + $i;
    if (($indice >= 0) AND ($indice < $max)) {
      array_push($stack, $speed[$indice]);
    }
  }

  return array_sum($stack)/count($stack);
}

function median($array) {
    rsort($array); 
    $middle = round(count($array) / 2); 
    return $array[$middle-1]; 
}                 

function compute_median($speed, $value, $max, $count = 3) {
  $nb_val = 0;
  $stack = array();

  for ($i = -$count; $i <= $count; $i++) {
    $indice = $value + $i;
    if (($indice >= 0) AND ($indice < $max)) {
      array_push($stack, $speed[$indice]);
    }
  }

  return median($stack);
}

function sec2hms ($sec)
{
  $hms = "";
  $hours = intval(intval($sec) / 3600); 
  $hms .= str_pad($hours, 2, "0", STR_PAD_LEFT). ":";
  $minutes = intval(($sec / 60) % 60); 
  $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ":";
  $seconds = intval($sec % 60); 
  $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
  return $hms;
}

class NikePlusPHPPlotrun extends NikePlusPHP {
    ##############################################################
    ########################### BVA ##############################
    ##############################################################
    public function get_basic_info($index = 0, $count = 10) {
        $indexEnd = $index + $count;
        $results = $this->_getNikePlusFile('http://nikeplus.nike.com/plus/activity/running/'.rawurlencode($this->userId).'/lifetime/activities?indexStart='.$index.'&indexEnd='.$indexEnd);

        if(isset($results->activities)) {
            arsort($results->activities);
            foreach($results->activities as $a) {
                // print_r($a);

                $distance = number_format($a->activity->metrics->distance, 2);
                $duration = sec2hms($a->activity->metrics->duration/1000);
                if (($timestamp = strtotime($a->activity->startTimeUtc)) === false) {
                  $time_str = "Date inconnue";
                } else {
                  $time_str = strftime('%d/%m/%Y %H:%M', $timestamp);
                }

                // $properties = get_object_vars($a->activity);
                // print_r($properties);
                // print '<br/><br/>';

                $runArray[] = array(
                    'runId'=> (string) $a->activity->activityId,
                    'distance'=> $distance,
                    'duration'=> $duration,
                    'calories'=> $a->activity->metrics->calories,
                    'time_str'=> $time_str,
                    'gps'=> $a->activity->gps,
                    'heartrate'=> $a->activity->heartrate,
                );
            }
            return $runArray;
        }
        else {
            return false;
        }
    }

    public function latest() {
        $results = $this->get_basic_info(0, 1);
        if(isset($results)) {
            return reset($results);
        }
        else {
            return false;
        }
    }

    /*
    ** print_full_data
    */
    public function getRunData($runId) {
        $runData = $this->activity($runId);

        $out = array();
        $out['GPS'] = $runData->activity->gps;
        $out['total_duration'] = $runData->activity->duration;
        $out['total_distance'] = $runData->activity->distance;
        $out['start_time'] = $runData->activity->startTimeUtc;

        foreach($runData->activity->history as $history_info){
            if ($history_info->type == 'DISTANCE'){
                $out['DISTANCE'] = $history_info->values;
            }
            else if ($history_info->type == 'HEARTRATE'){
                $out['HEARTRATE'] = $history_info->values;
            }
        }
        return $out;
    }

    public function get_run_data($run_id, $x_axis = False)
    {
      date_default_timezone_set('Europe/Paris');
      setlocale (LC_TIME, 'fr_FR');

      $run_data = $this->getRunData($run_id);

      $out = array();

      $out['title'] = "Entrainement du ".strftime(
        '%A %d/%m/%Y %H:%M',
        strtotime($run_data['start_time'])
      );

      $raw_stack = array();
      $avg_stack_2 = array();
      $avg_stack_3 = array();
      $avg_stack_4 = array();
      $avg_stack_5 = array();
      $median_stack = array();
      $avg_fake = array();
      $bpm_stack = array();

      $dist = $run_data['DISTANCE'];

      $bpm = NULL;
      if (isset($run_data['HEARTRATE'])) {
        $bpm = $run_data['HEARTRATE'];
      }

      $array_count = count($dist);

      // $km_iterator = 1;
      for ($i = 0; $i < $array_count - 1; $i++) {
        $speed = ( $dist[$i+1] - $dist[$i] ) * 360;
        $time = $i / 6;
        array_push ($raw_stack, $speed);
        // if ( ($dist[$i+1] >= $km_iterator ) && ($dist[$i] < $km_iterator ) ) {
        //   $line = new PlotLine(VERTICAL, $time, "green", 1);
        //   $graph->AddLine($line);
        //   $km_iterator += 1;
        // }
      }

      $average_speed = $dist[$array_count - 1] / ($array_count - 1) * 360;
      if($average_speed == 0)
        $pace = 0;
      else
        $pace = 1 / $average_speed * 60;

      $pace_minutes = intval($pace);
      $pace_sec = round (($pace - $pace_minutes)*60);
      $pace_seconds = sprintf("%02d", $pace_sec);
      $average_speed = round($average_speed, 2);

      $total_duration = sec2hms($run_data['total_duration']/1000);
      $total_distance = number_format($run_data['total_distance'], 2);


      // Add the subtitle to the graph
      $subtitle  = "Vitesse moyenne : ".$average_speed." km/h - ";
      $subtitle .= "Rythme : ".$pace_minutes.":".$pace_seconds." m/km<br/>";
      $subtitle .= "Distance totale : ".$total_distance." km - ";
      $subtitle .= "Duree totale : ".$total_duration;
      $subtitle .= "<br/><i>Sélectionnez une zone pour zoomer</i>";

      $out['subtitle'] = sprintf('\'%s\'', $subtitle);
      $out['gps'] = $run_data['GPS'];

      $last_valid_bpm = 100;

      for ($i = 0; $i < $array_count - 1; $i++) {
        $raw_data[(string)$dist[$i]] = $raw_stack[$i];

        // $avg_stack_2[(string)$dist[$i]] = compute_avg($raw_stack, $i, $array_count - 1, 2);
        // $avg_stack_3[(string)$dist[$i]] = compute_avg($raw_stack, $i, $array_count - 1, 3);
        $avg_stack_4[(string)$dist[$i]] = compute_avg($raw_stack, $i, $array_count - 1, 4);
        // $avg_stack_5[(string)$dist[$i]] = compute_avg($raw_stack, $i, $array_count - 1, 5);
        $median_stack[(string)$dist[$i]] = compute_median($raw_stack, $i, $array_count - 1, 2);

        // $avg_fake[$dist[i]] = $average;

        if ($bpm != NULL) {
          $current_bpm = $bpm[$i];
          if ($current_bpm == 0)
            $bpm_stack[(string)$dist[$i]] = $last_valid_bpm;
          else {
            $bpm_stack[(string)$dist[$i]] = $current_bpm;
            $last_valid_bpm = $current_bpm;
          }
        }
      }

      // var_dump($raw_stack);

      $format = '{name: \'%s\', data: [%s], pointInterval: 10, %s},';
      
      if ($x_axis) {
        $out['serie'] .= sprintf($format, 'Vitesse brute', implode(", ", array_values($raw_stack)), '');
        // $out['serie'] .= sprintf($format, 'Vitesse lissée (moyenne 2)', implode(", ", array_values($avg_stack_2)), '');
        // $out['serie'] .= sprintf($format, 'Vitesse lissée (moyenne 3)', implode(", ", array_values($avg_stack_3)), '');
        $out['serie'] .= sprintf($format, 'Vitesse lissée (moyenne)', implode(", ", array_values($avg_stack_4)), '');
        // $out['serie'] .= sprintf($format, 'Vitesse lissée (moyenne 5)', implode(", ", array_values($avg_stack_5)), '');

        $out['serie'] .= sprintf($format, 'Vitesse lissée (médiane)', implode(", ", array_values($median_stack)), '');
        // $out['serie'] .= sprintf('{name: \'Vitesse moyenne\', data: [[0, %s], [%s, %s]]},', $average_speed, ($array_count - 1) * 10, $average_speed);    
      }
      else {
        $out['serie'] .= sprintf($format, 'Vitesse brute', implode(", ", $raw_stack), '');
        // $out['serie'] .= sprintf($format, 'Vitesse lissée (moyenne 2)', implode(", ", $avg_stack_2), '');
        // $out['serie'] .= sprintf($format, 'Vitesse lissée (moyenne 3)', implode(", ", $avg_stack_3), '');
        $out['serie'] .= sprintf($format, 'Vitesse lissée (moyenne)', implode(", ", $avg_stack_4), '');
        // $out['serie'] .= sprintf($format, 'Vitesse lissée (moyenne 5)', implode(", ", $avg_stack_5), '');

        $out['serie'] .= sprintf($format, 'Vitesse lissée (médiane)', implode(", ", $median_stack), '');
        // $out['serie'] .= sprintf('{name: \'Vitesse moyenne\', data: [[0, %s], [%s, %s]]},', $average_speed, ($array_count - 1) * 10, $average_speed);    
      }


      if ($bpm != NULL){
        $out['bpm'] = True;
        $out['serie'] .= sprintf($format, 'Fréquence cardiaque', implode(", ", $bpm_stack), 'yAxis: 1,');
      }

      return $out;
    }

  /**
     * toGpx()
     * outputs a run object to a runtastic importable gpx document
     *
     * @param object $run output from run()
     *
     * @return string gpx string
     */
  public function toGpx($run) {                
    $activity = $run->activity;
    $waypoints = $activity->geo->waypoints;
    $startTime = strtotime($activity->startTimeUtc);
    $name = 'Nike+ ' . $activity->name;
    $description = $activity->tags->note;
    $heartRate = null;
    $distance = null;
     
    foreach($run->activity->history as $hi) {
      if($hi->type == 'HEARTRATE') $heartRate = $hi;
      if($hi->type == 'DISTANCE') $distance = $hi;
    }
 
    $b = new XMLWriter();
    $b->openMemory();  
    $b->setIndent(true);    
    $b->setIndentString("    ");
    $b->startDocument("1.0", "UTF-8");
                        
    $b->startElement('gpx');  
    $b->writeAttribute('version', '0.1');
    $b->writeAttribute('creator', 'nikeplusphp');
    $b->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $b->writeAttribute('xmlns', 'http://www.topografix.com/GPX/1/1');
    $b->writeAttribute('xsi:schemaLocation', 'http://www.topografix.com/GPX/1/1 http://www.topografix.com/gpx/1/1/gpx.xsd');
    $b->writeAttribute('xmlns:gpxtpx', 'http://www.garmin.com/xmlschemas/TrackPointExtension/v1'); 
    $b->writeAttribute('xmlns:gpxx', 'http://www.garmin.com/xmlschemas/GpxExtensions/v3');    
     
    // metadata
    $b->startElement('metadata');    
    $b->writeElement('name', $name);
    $b->writeElement('desc', $description);     
    // get min/max lat/lng                               
    $minLon = 10000;
    $maxLon = -10000;
    $minLat = 10000;
    $maxLat = -10000;
    foreach($waypoints as $wp) {
      if($wp->lon > $maxLon) $maxLon = $wp->lon;
      if($wp->lon < $minLon) $minLon = $wp->lon;
      if($wp->lat > $maxLat) $maxLat = $wp->lat;
      if($wp->lat < $minLat) $minLat = $wp->lat;
    }
    $b->startElement('bounds');
    $b->writeAttribute('maxlon', $maxLon);
    $b->writeAttribute('minlon', $minLon);
    $b->writeAttribute('maxlat', $maxLat);
    $b->writeAttribute('minlat', $minLat);
    $b->endElement(); // EO bounds        
     
    $b->endElement(); // EO metadata
        
    // track
    $b->startElement('trk');    
    $b->writeElement('name', 'trkName ' . time());
    $b->writeElement('type', 'Run');         
                        
    $b->startElement('trkseg');
 
    foreach($waypoints as $index => $wp) {
      $b->startElement('trkpt');
      $b->writeAttribute('lat', $wp->lat);
      $b->writeAttribute('lon', $wp->lon);
      $b->writeElement('ele', $wp->ele); 
      $b->writeElement('time', date('Y-m-d\TH:i:s', $startTime+$index));
             
      $b->startElement('extensions');        
      $b->startElement('gpxtpx:TrackPointExtension');     
 
      if($heartRate !== null) {
        $hrKey = (int) floor($index/$heartRate->intervalMetric);
        if(array_key_exists($hrKey, $heartRate->values)) {
          $b->writeElement('gpxtpx:hr', $heartRate->values[$hrKey]);
        }
      }
      $b->endElement(); // EO gpxtpx:TrackPointExtension
      $b->endElement(); // EO extensions    
       
      $b->endElement(); // EO trkpt
    }
     
    $b->endElement(); // EO trkseg
    $b->endElement(); // EO trk
     
     
    $b->endElement(); // EO gpx       
                                      
     
    $b->endDocument();
    return $b->outputMemory();
 
   }
}