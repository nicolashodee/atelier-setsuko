<?php
/*
 * Plus XML options import/export
 *
 * @package Cryout Plus
 */

/**
 * Hooked into init to generate and output the export XML file
 */
function cryout_export_options(){

    if (ob_get_contents()) ob_clean();

	/* Check authorisation */
	$authorised = true;
	// Check nonce
	if ( ! wp_verify_nonce( $_POST['cryout-export'], 'cryout-export' ) ) {
		$authorised = false;
	}
	// Check permissions
	if ( ! current_user_can( 'edit_theme_options' ) ){
		$authorised = false;
	}

	if ( $authorised) {
        
        date_default_timezone_set('UTC');
		
		$siteurl = get_option('siteurl');
		$datetime = date('Ymd-His');
		
        $name = _CRYOUT_THEME_NAME.'-options-'.preg_replace("/[^a-z0-9-_]/i",'',preg_replace("/https?\:\/\//","",$siteurl)).'-'.$datetime.'.xml';
		$data = cryout_get_theme_options();
		unset($data[_CRYOUT_THEME_SLUG . '_db']);
		$data = array_merge( array(_CRYOUT_THEME_NAME . '_db' => '0.9'), $data);
		$xml = Cryout_Array2XML::createXML(_CRYOUT_THEME_NAME . '-settings', $data);

		header( 'Content-Type: text/xml' );
		header( 'Content-Disposition: attachment; filename="'.$name.'"' );
		header( "Content-Transfer-Encoding: binary" );
		header( 'Accept-Ranges: bytes' );

		// the three lines below basically make the download non-cacheable 
		header( "Cache-control: private" );
		header( 'Pragma: private' );
		header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );

		//header( "Content-Length: " . sizeof($xml) );
		print ( $xml->saveXML() );
		print ( '<!-- Generated ' . $datetime . ' from ' . $siteurl . ' running ' . cryout_sanitize_tnl(_CRYOUT_THEME_NAME) . ' v' . _CRYOUT_THEME_VERSION . ' -->' );
}
die();
} // cryout_export_options()

/**
 * Manages the theme options upload and import operations.
 * Uses the theme page to create a new form for uploading the options
 * Uses WP_Filesystem
*/
function cryout_import_form(){

    $bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
    $size = size_format( $bytes );
    $upload_dir = wp_upload_dir();
    if ( ! empty( $upload_dir['error'] ) ) :
        ?><div class="error"><p><?php _e('Before you can use this functionality, you will need to fix the following error:', 'cryout'); ?></p>
            <p><strong><?php echo $upload_dir['error']; ?></strong></p></div><?php
    else :
    ?>
    <div class="wrap">
        <h2><?php echo __( 'Import Theme Options', 'cryout' );?></h2>
		<div style="display: inline-block; padding: 2em; background: #fff;">
        <div id="icon-tools" class="icon32"><br></div>
        <form enctype="multipart/form-data" id="import-upload-form" method="post" action="">
        	<p style="padding: 1em; border: 1px solid #880000; color: #880000;"><strong><?php _e('This will overwrite all existing theme options.', 'cryout'); ?></strong></p>
            <p>
                <label for="upload"><strong><?php _e('Select theme options XML file', 'cryout'); ?>:</strong></label><br><br>
		        <input type="file" id="upload" name="import" size="25" />
				<span style="font-size:11px;">(<?php  printf( __( 'Maximum size: %s', 'cryout' ), $size ); ?> )</span>
                <input type="hidden" name="action" value="save" />
                <input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
                <?php wp_nonce_field('cryout-import', 'cryout-import'); ?>
                <input type="hidden" name="cryout_import" value="true" />
            </p><br>
            <input type="submit" class="button" value="<?php _e('Import Options', 'cryout'); ?>" style="display: table; margin: 0 auto;"/>
        </form>
	</div>
    </div> <!-- end wrap -->
    <?php
    endif;
} // cryout_import_form()

/**
 * Imports options from file to options array.
*/
function cryout_import_process() {
	
	// were we called?
	if ( empty($_POST['cryout-import']) ) return;

    // verify authorization to import
    if (!wp_verify_nonce($_POST['cryout-import'], 'cryout-import') || !current_user_can('edit_theme_options') ) { 
		wp_die(__('ERROR: You are not authorised to perform that operation', 'cryout'));
	}

	$current_settings = cryout_get_theme_options();
	$result = 0;

	// make sure there is an import file uploaded
	if ( (isset($_FILES["import"]["size"]) &&  ($_FILES["import"]["size"] > 0) ) ) {

		$form_fields = array('import');
		$method = '';

		$url = wp_nonce_url( _CRYOUT_THEME_PAGE_URL, 'cryout-import');

		// Get file writing credentials
		if (false === ($creds = request_filesystem_credentials($url, $method, false, false, $form_fields) ) ) {
			return true;
		}

		if ( ! WP_Filesystem($creds) ) {
			// our credentials were no good, ask the user for them again
			request_filesystem_credentials($url, $method, true, false, $form_fields);
			return true;
		}

		// Write the file if credentials are good
		$upload_dir = wp_upload_dir();
		$filename = trailingslashit($upload_dir['path'])._CRYOUT_THEME_NAME.'-options-import-'.date('Ymd-His').'.xml';

		// by this point, the $wp_filesystem global should be working, so let's use it to create a file
		global $wp_filesystem;
		if ( ! $wp_filesystem->move($_FILES['import']['tmp_name'], $filename, true) ) {
			echo 'Error saving file!';
			return;
		}

		$file = $_FILES['import'];

		if ($file['type'] == 'text/xml') {

			$data = $wp_filesystem->get_contents($filename);
			
			// try to read the file
			if ($data !== FALSE) {
				
				$settings = Cryout_XML2Array::createArray($data);
				$settings = $settings[_CRYOUT_THEME_NAME."-settings"];
				
				// try to read the settings array
				if ( isset( $settings[_CRYOUT_THEME_NAME . '_db'] ) ){
					
					$settings = wp_parse_args($settings, $current_settings);
					delete_option( _CRYOUT_THEME_NAME.'_settings' );
					
					if ( update_option( _CRYOUT_THEME_NAME.'_settings', $settings ) ) {
						// success
						$result = 1;
						wp_redirect( admin_url( _CRYOUT_THEME_PAGE_URL . '#options') );
					} else {
						// settings update failed
						$result = 2;
					}
					
				} else { 
					// read the settings array failed
					$result = 3;
				}
			} else { 
				// read the file failed
				$result = 4;
			}
		} else { 
			// the file uploaded is not a plain text file
			$result = 5;
		}
		// delete the file after we're done
		$wp_filesystem->delete($filename);
	} else { 
		// no import file uploaded
		$result = 6;
	}
	
	set_transient( '_cryout_' . _CRYOUT_THEME_SLUG . '_import_result', $result, 60 );
	//die(var_dump($result));
	
} // cryout_import_process()

function cryout_import_result( $result = 0 ) {
	if (empty($result)) $result = get_transient( '_cryout_' . _CRYOUT_THEME_SLUG . '_import_result', 0 );
	if (empty($result)) return;
	if ($result > 1) delete_transient( '_cryout_' . _CRYOUT_THEME_SLUG . '_import_result' );
	switch ($result) { 
		case 1:
			cryout_import_result_message( __('Theme options have been imported succesfully.', 'cryout'), 'updated', __('Options Import', 'cryout') ); 
		case 2: 
			cryout_import_result_message( __('Theme options import failed.', 'cryout'), 'error' ); 
			break;
		case 3: 
			cryout_import_result_message( __('The uploaded file does not contain valid theme options. Please recheck the file.', 'cryout'), 'error' ); 
			break;
		case 4:
			cryout_import_result_message( __('The uploaded file could not be read.', 'cryout'), 'error' ); 
			break;
		case 5:
			cryout_import_result_message( __('The uploaded file is not supported. Make sure the file was exported from the correct theme and that it is an XML file.', 'cryout'), 'error' ); 
			break;
		case 6:
			cryout_import_result_message( __('The file is empty or there was no file. This error can also be caused by uploads being disabled on your server or by post_max_size being defined as smaller than upload_max_filesize in php.ini.', 'cryout'), 'error' ); 
			break;
		default:
		break;
	} // switch	
} // cryout_import_result()

function cryout_import_result_message( $message, $type='info', $title = '' ) { 
	if ( empty( $title) ) $title = __('Oops, there was a problem!', 'cryout'); 
	?>
	<div class="wrap">
		<div id="icon-tools" class="icon32"><br></div>
		<h2><?php echo __( 'Import Theme Options', 'cryout' );?></h2> 
		<div class="notice notice-<?php echo $type ?>">
			<p><strong><?php echo $title ?></strong></p>
			<p><?php echo $message ?></p>
		</div>
	</div> <!-- end wrap -->
<?php
} // cryout_import_result_message()



/************************** Array2XML Class /**************************/



/**
 * Array2XML: A class to convert array in PHP to XML
 * It also takes into account attributes names unlike SimpleXML in PHP
 * It returns the XML in form of DOMDocument class for further manipulation.
 * It throws exception if the tag name or attribute name has illegal chars.
 *
 * Author : Lalit Patel
 * Website: http://www.lalit.org/lab/convert-php-array-to-xml-with-attributes
 * License: Apache License 2.0
 *          http://www.apache.org/licenses/LICENSE-2.0
 * Version: 0.1 (10 July 2011)
 * Version: 0.2 (16 August 2011)
 *          - replaced htmlentities() with htmlspecialchars() (Thanks to Liel Dulev)
 *          - fixed a edge case where root node has a false/null/0 value. (Thanks to Liel Dulev)
 * Version: 0.3 (22 August 2011)
 *          - fixed tag sanitize regex which didn't allow tagnames with single character.
 * Version: 0.4 (18 September 2011)
 *          - Added support for CDATA section using @cdata instead of @value.
 * Version: 0.5 (07 December 2011)
 *          - Changed logic to check numeric array indices not starting from 0.
 * Version: 0.6 (04 March 2012)
 *          - Code now doesn't @cdata to be placed in an empty array
 * Version: 0.7 (24 March 2012)
 *          - Reverted to version 0.5
 * Version: 0.8 (02 May 2012)
 *          - Removed htmlspecialchars() before adding to text node or attributes.
 *
 * Usage:
 *       $xml = Array2XML::createXML('root_node_name', $php_array);
 *       echo $xml->saveXML();
 */

class Cryout_Array2XML {

    private static $xml = null;
	private static $encoding = 'UTF-8';

    /**
     * Initialize the root XML node [optional]
     * @param $version
     * @param $encoding
     * @param $format_output
     */
    public static function init($version = '1.0', $encoding = 'UTF-8', $format_output = true) {
        self::$xml = new DomDocument($version, $encoding);
        self::$xml->formatOutput = $format_output;
		self::$encoding = $encoding;
    }

    /**
     * Convert an Array to XML
     * @param string $node_name - name of the root node to be converted
     * @param array $arr - aray to be converterd
     * @return DomDocument
     */
    public static function &createXML($node_name, $arr=array()) {
        $xml = self::getXMLRoot();
        $xml->appendChild(self::convert($node_name, $arr));

        self::$xml = null;    // clear the xml node in the class for 2nd time use.
        return $xml;
    }

    /**
     * Convert an Array to XML
     * @param string $node_name - name of the root node to be converted
     * @param array $arr - aray to be converterd
     * @return DOMNode
     */
    private static function &convert($node_name, $arr=array()) {

        //print_arr($node_name);
        $xml = self::getXMLRoot();
        $node = $xml->createElement($node_name);

        if(is_array($arr)){
            // get the attributes first.;
            if(isset($arr['@attributes'])) {
                foreach($arr['@attributes'] as $key => $value) {
                    if(!self::isValidTagName($key)) {
						continue; // skip invalid attribute
                        //throw new Exception('[Array2XML] Illegal character in attribute name. attribute: "'.$key.'" in node: "'.$node_name.'"');
                    }
                    $node->setAttribute($key, self::bool2str($value));
                }
                unset($arr['@attributes']); //remove the key from the array once done.
            }

            // check if it has a value stored in @value, if yes store the value and return
            // else check if its directly stored as string
            if(isset($arr['@value'])) {
                $node->appendChild($xml->createTextNode(self::bool2str($arr['@value'])));
                unset($arr['@value']);    //remove the key from the array once done.
                //return from recursion, as a note with value cannot have child nodes.
                return $node;
            } else if(isset($arr['@cdata'])) {
                $node->appendChild($xml->createCDATASection(self::bool2str($arr['@cdata'])));
                unset($arr['@cdata']);    //remove the key from the array once done.
                //return from recursion, as a note with cdata cannot have child nodes.
                return $node;
            }
        }

        //create subnodes using recursion
        if(is_array($arr)){
            // recurse to get the node for that key
            foreach($arr as $key=>$value){
                if(!self::isValidTagName($key)) {
					continue; // skip invalid tag
					// throw new Exception('[Array2XML] Illegal character in tag name. tag: "'.$key.'" in node: "'.$node_name.'"');
                }
                if(is_array($value) && is_numeric(key($value))) {
                    // MORE THAN ONE NODE OF ITS KIND;
                    // if the new array is numeric index, means it is array of nodes of the same kind
                    // it should follow the parent key name
                    foreach($value as $k=>$v){
                        $node->appendChild(self::convert($key, $v));
                    }
                } else {
                    // ONLY ONE NODE OF ITS KIND
                    $node->appendChild(self::convert($key, $value));
                }
                unset($arr[$key]); //remove the key from the array once done.
            }
        }

        // after we are done with all the keys in the array (if it is one)
        // we check if it has any text value, if yes, append it.
        if(!is_array($arr)) {
            $node->appendChild($xml->createTextNode(self::bool2str($arr)));
        }

        return $node;
    }

    /*
     * Get the root XML node, if there isn't one, create it.
     */
    private static function getXMLRoot(){
        if(empty(self::$xml)) {
            self::init();
        }
        return self::$xml;
    }

    /*
     * Get string representation of boolean value
     */
    private static function bool2str($v){
        //convert boolean to text value.
        $v = $v === true ? 'true' : $v;
        $v = $v === false ? 'false' : $v;
        return $v;
    }

    /*
     * Check if the tag name or attribute name contains illegal characters
     * Ref: http://www.w3.org/TR/xml/#sec-common-syn
     */
    private static function isValidTagName($tag){
        $pattern = '/^[a-z_]+[a-z0-9\:\-\.\_]*[^:]*$/i';
        return preg_match($pattern, $tag, $matches) && $matches[0] == $tag;
    }
}



/**
 * XML2Array: A class to convert XML to array in PHP
 * It returns the array which can be converted back to XML using the Array2XML script
 * It takes an XML string or a DOMDocument object as an input.
 *
 * See Array2XML: http://www.lalit.org/lab/convert-php-array-to-xml-with-attributes
 *
 * Author : Lalit Patel
 * Website: http://www.lalit.org/lab/convert-xml-to-array-in-php-xml2array
 * License: Apache License 2.0
 *          http://www.apache.org/licenses/LICENSE-2.0
 * Version: 0.1 (07 Dec 2011)
 * Version: 0.2 (04 Mar 2012)
 * 			Fixed typo 'DomDocument' to 'DOMDocument'
 *
 * Usage:
 *       $array = XML2Array::createArray($xml);
 */

class Cryout_XML2Array {

    private static $xml = null;
	private static $encoding = 'UTF-8';

    /**
     * Initialize the root XML node [optional]
     * @param $version
     * @param $encoding
     * @param $format_output
     */
    public static function init($version = '1.0', $encoding = 'UTF-8', $format_output = true) {
        self::$xml = new DOMDocument($version, $encoding);
        self::$xml->formatOutput = $format_output;
		self::$encoding = $encoding;
    }

    /**
     * Convert an XML to Array
     * @param string $node_name - name of the root node to be converted
     * @param array $arr - aray to be converterd
     * @return DOMDocument
     */
    public static function &createArray($input_xml) {
        $xml = self::getXMLRoot();
		if(is_string($input_xml)) {
			$parsed = $xml->loadXML($input_xml);
			if(!$parsed) {
				throw new Exception('[XML2Array] Error parsing the XML string.');
			}
		} else {
			if(get_class($input_xml) != 'DOMDocument') {
				throw new Exception('[XML2Array] The input XML object should be of type: DOMDocument.');
			}
			$xml = self::$xml = $input_xml;
		}
		$array[$xml->documentElement->tagName] = self::convert($xml->documentElement);
        self::$xml = null;    // clear the xml node in the class for 2nd time use.
        return $array;
    }

    /**
     * Convert an Array to XML
     * @param mixed $node - XML as a string or as an object of DOMDocument
     * @return mixed
     */
    private static function &convert($node) {
		$output = array();

		switch ($node->nodeType) {
			case XML_CDATA_SECTION_NODE:
				$output['@cdata'] = trim($node->textContent);
				break;

			case XML_TEXT_NODE:
				$output = trim($node->textContent);
				break;

			case XML_ELEMENT_NODE:

				// for each child node, call the covert function recursively
				for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
					$child = $node->childNodes->item($i);
					$v = self::convert($child);
					if(isset($child->tagName)) {
						$t = $child->tagName;

						// assume more nodes of same kind are coming
						if(!isset($output[$t])) {
							$output[$t] = array();
						}
						$output[$t][] = $v;
					} else {
						//check if it is not an empty text node
						if($v !== '') {
							$output = $v;
						}
					}
				}

				if(is_array($output)) {
					// if only one node of its kind, assign it directly instead if array($value);
					foreach ($output as $t => $v) {
						if(is_array($v) && count($v)==1) {
							$output[$t] = $v[0];
						}
					}
					if(empty($output)) {
						//for empty nodes
						$output = '';
					}
				}

				// loop through the attributes and collect them
				if($node->attributes->length) {
					$a = array();
					foreach($node->attributes as $attrName => $attrNode) {
						$a[$attrName] = (string) $attrNode->value;
					}
					// if its an leaf node, store the value in @value instead of directly storing it.
					if(!is_array($output)) {
						$output = array('@value' => $output);
					}
					$output['@attributes'] = $a;
				}
				break;
		}
		return $output;
    }

    /*
     * Get the root XML node, if there isn't one, create it.
     */
    private static function getXMLRoot(){
        if(empty(self::$xml)) {
            self::init();
        }
        return self::$xml;
    }
}

// FIN