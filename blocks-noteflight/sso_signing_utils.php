<?php
/*
 * Copyright (c) 2008, 2009 Noteflight LLC
 *
 *  This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 2 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

# Generates a signed single sign-on link starting with the given
# base_url, adding the user_id/role/name, and signing it with the
# secret key.  The optional expiration is the time that should
# elapse (in seconds) before the link becomes invalid.
function create_sso_url($base_url,
                        $sso_user_id,
                        $sso_user_role,
                        $sso_username,
                        $secret_key,
                        $expiration = 120) {
  # Deconstruct the url
  $parts = sso_deconstruct_url($base_url);

  # Remove any existing sso_ parameters
  $sso_params = array('sso_user_id',
                      'sso_user_role',
                      'sso_username',
                      'sso_expiration',
                      'sso_nonce',
                      'sso_version',
                      'sso_signature');
  $newparams = array();
  foreach($parts['params'] as $param) {
    if (!in_array($param['name'], $sso_params)) {
      array_push($newparams,
                 array('name' => $param['name'],
                       'value' => $param['value']));
    }
  }
  $parts['params'] = $newparams;

  # Create the nonce
  $nonce = substr(sha1("" . rand()), 0, 8);

  # Add the parameters
  array_push($parts['params'], array('name' => 'sso_user_id', 'value' => $sso_user_id));
  array_push($parts['params'], array('name' => 'sso_user_role', 'value' => $sso_user_role));
  array_push($parts['params'], array('name' => 'sso_username', 'value' => $sso_username));
  array_push($parts['params'], array('name' => 'sso_expiration', 'value' => '' . (time() + $expiration)));
  array_push($parts['params'], array('name' => 'sso_version', 'value' => '1.0'));
  array_push($parts['params'], array('name' => 'sso_nonce', 'value' => $nonce));

  # Create the unsigned url
  $unsigned_url = sso_construct_url($parts);

  # Sign the url
  return sso_sign_url($unsigned_url, $secret_key);
  }

# "Strips" the url by removing all of the sso parameters
function strip_sso_url($url) {
  # Deconstruct the url
  $parts = sso_deconstruct_url($url);

  # Remove any existing sso_ parameters
  $sso_params = array('sso_user_id',
                      'sso_user_role',
                      'sso_username',
                      'sso_expiration',
                      'sso_nonce',
                      'sso_version',
                      'sso_signature');
  $newparams = array();
  foreach($parts['params'] as $param) {
    if (!in_array($param['name'], $sso_params)) {
      array_push($newparams,
                 array('name' => $param['name'],
                       'value' => $param['value']));
    }
  }
  $parts['params'] = $newparams;

  # Reconstruct the url
  return sso_construct_url($parts);
  }

# Signs and returns the given url by adding the appropriate
# sso_signature
function sso_sign_url($url, $secret_key) {
  return sso_add_parameter($url, 'sso_signature', sso_get_url_signature($url, $secret_key));
  }

# Compares two strings
function sso_compare_strings($s1, $s2) {
  return ($s1 < $s2) ? -1 : (($s1 > $s2) ? 1 : 0);
  }

# Compares two parameters
function sso_compare_params($p1, $p2) {
  $ret = sso_compare_strings($p1['name'], $p2['name']);
  if($ret == 0) {
    $ret = sso_compare_strings($p1['value'], $p2['value']);
  }
  return $ret;
}

# Returns the signature that should be used for the given url
function sso_get_url_signature($url, $secret_key) {
  $parts = sso_deconstruct_url($url);

  # Normalize the request parameters
  $params = $parts['params'];

  # Remove any existing sso_signature
  $params = sso_remove_parameter($params, "sso_signature");

  # Encode all parameters
  $newparams = array();
  foreach($params as $param) {
    array_push($newparams,
               array('name' => sso_escape($param['name']),
                     'value' => sso_escape($param['value'])));
  }
  $params = $newparams;

  # Sort by name, value
  usort($params, "sso_compare_params");

    # Form the final parameters string
  $parameters = "";
  for($i = 0; $i < count($params); $i++) {
    $param = $params[$i];
    if ($i > 0) {
      $parameters = $parameters . "&";
    }
    $parameters = $parameters . $params[$i]['name'] . "=" . $params[$i]['value'];
  }

  # Form the request url
  $scheme = strtolower($parts['scheme']);
  $port = sso_empty_string($parts['port']) ? "" : $parts['port'];
  # Only include the port if it is not the default
  $port_str =
    (sso_empty_string($parts['port']) ||
     ($scheme == 'http' && $port == '80') ||
     ($scheme == 'https' && $port == '443')) ?
    '' : (":" . $port);
  # Construct the request_url
  $request_url = $scheme . '://' . strtolower($parts['hostname']) . $port_str . $parts['path'];

  # Form the final signature base string
  $signature_base_string =
    "GET&" .
    sso_escape($request_url) .
    "&" .
    sso_escape($parameters);

  # Return the signature
  return sso_sign_hmac($signature_base_string, sso_escape($secret_key));
}

# Parses the given url and returns a hash of the results
#
#  scheme
#  hostname
#  port
#  path
#  params
#  fragment
#
# The params are taken from the query argument and are an array of
# {'name' => ..., 'value' => ...} pairs (there may be multiple params
# with the same :name).
function sso_deconstruct_url($url) {
  # Split the URI and extract the pieces
  $uriparts = parse_url($url);
  $query = $uriparts['query'];
  $ret = array('scheme' => $uriparts['scheme'],
               'hostname' => $uriparts['host'],
               'port' => $uriparts['port'],
               'path' => $uriparts['path'],
               'fragment' => $uriparts['fragment']);

  if(sso_empty_string($query)) {
    $params = array();
  }
  else {
    $params = array();
    foreach(split("&", $query) as $param) {
      $param_pair = split("=", $param);
      $elem = array('name' => urldecode($param_pair[0]), 'value' => urldecode($param_pair[1]));
      array_push($params, $elem);
    }
  }
  $ret['params'] = $params;

  return $ret;
  }

# Takes an array of parameter {'name' => ..., 'value' => ...} pairs
# and returns a hash mapping name to value.  If there are multiple
# values for the same name, only one of the values is put into the
# hash (it is undefined as to which one)
function sso_params_hash($params_array) {
  $ret = array();
  foreach($params_array as $param) {
    $ret[$param['name']] = $param['value'];
  }
  return $ret;
}

# Adds a single name/value parameter to the given url (replacing any
# existing parameter with the same name) and returns the resulting
# url.
function sso_add_parameter($url, $name, $value) {
  $parts = sso_deconstruct_url($url);
  $params = sso_remove_parameter($parts['params'], $name);
  array_push($params, array('name' => $name, 'value' => $value));
  $parts['params'] = $params;
  return sso_construct_url($parts);
}

# Returns an array with any parameters with the given name removed
function sso_remove_parameter($params, $name) {
  $ret = array();
  foreach($params as $param) {
    if($param['name'] != $name) {
      array_push($ret, $param);
    }
  }
  return $ret;
}

# Constructs a url from the given parameters.  The parameters are
# the same as those from deconstruct_url
function sso_construct_url($urlparams) {
  # Add scheme, hostname, port
  $ret = $urlparams['scheme'] . "://" . $urlparams['hostname'];
  if(!sso_empty_string($urlparams['port'])) {
    $ret = $ret . ":" . $urlparams['port'];
  }

  # Add path
  if (!sso_empty_string($urlparams['path'])) {
    $ret = $ret . $urlparams['path'];
  }

  # Add params
  $params = $urlparams['params'];
  if($params != NULL) {
    for($i = 0; $i < count($params); $i++) {
      $param = $params[$i];
      $ret = $ret . (($i > 0) ? "&" : "?") . sso_escape($param['name']) . '=' . sso_escape($param['value']);
    }
  }

  # Add fragment
  if (!sso_empty_string($urlparams['fragment'])) {
    $ret = $ret . "#" . $urlparams['fragment'];
  }

  return $ret;
}

# Returns the HMAC-SHA1 signature of the given text, using the given key
function sso_sign_hmac($text, $key) {
  $digest = hash_hmac("sha1", $text, $key, TRUE);
  return base64_encode($digest);
  }

# Performs url-escaping on a string, using the restricted character
# set of ALPHA, DIGIT, '-', '.', '_', '~'
function sso_escape($str) {
  # Note that this might not correctly encode just those characters
  # defined as RFC3986_UNRESERVED
  return rawurlencode($str);
}

# Returns if this is an empty string or not
function sso_empty_string($str) {
  return $str == NULL || strlen($str) == 0;
}

########
# Testing functions
########

function sso_array_is_associative ($array)
{
    if ( is_array($array) && ! empty($array) )
    {
        for ( $iterator = count($array) - 1; $iterator; $iterator-- )
        {
            if ( ! array_key_exists($iterator, $array) ) { return true; }
        }
        return ! array_key_exists(0, $array);
    }
    return false;
}


function sso_to_string($val) {
  if(sso_array_is_associative($val)) {
      $ret = "<table border=\"true\">";
      foreach($val as $name => $value) {
        $ret = $ret . "<tr><td>" . sso_to_string($name) . "</td><td>" . sso_to_string($value) . "</td></tr>";
      }
      $ret = $ret . "</table>";
      return $ret;
    }
  else if(is_array($val)) {
      $ret = "<ul>";
      foreach($val as $elem) {
        $ret = $ret . "<li>" . sso_to_string($elem) . "</li>";
      }
      $ret = $ret . "</ul>";
      return $ret;
    }
  else {
    return htmlentities($val);
  }
}
?>
