<?php

function humanized_duration($minutes, $numeric = false)
{
  $hours = floor($minutes / 60);
  $remainingMinutes = $minutes % 60;

  if ($numeric)
  {
    return sprintf('%d:%02d', $hours, $remainingMinutes);
  }

  if ($hours > 0 && $remainingMinutes > 0)
  {
    return sprintf('%dh %dm', $hours, $remainingMinutes);
  }
  elseif ($hours > 0)
  {
    return sprintf('%dh', $hours);
  }
  else
  {
    return sprintf('%dm', $remainingMinutes);
  }
}
