<?php
	abstract class AbstractBaseDataClass {

		/**
		 * Property containing strings
		 * @param $value String value.
		 */
		public function propertyStringValue($value) {
			if ($value == null) {
				return "null";
			}
			
			return "'" . mysql_escape_string($value) . "'";
		}

		/**
		 * Property containing integer
		 * @param $value Integer value.
		 */
		public function propertyIntValue($value) {
			if ($value == null) {
				return "null";
			}
			
			return $value;
		}

		/**
		 * Property containing double
		 * @param $value Double value.
		 */
		public function propertyDoubleValue($value) {
			return $this->propertyIntValue($value);
		}
		
		/**
		 * Property containing date
		 * @param $value Date value.
		 */
		public function propertyDateValue($value) {
			return $this->propertyStringValue($value);
		}

		/**
		 * Convert to JSON
		 */
		public function toJSON() {
			$json = array();
			
			foreach (get_object_vars($this) as $property => $value) {
				$annotations = $this->getAnnotations($property);
				
				if (in_array("date ", $annotations)) {
					$json[$property] = date("d/m/Y", strtotime($value));
				
				} else if (in_array("datetime ", $annotations)) {
					$json[$property] = date("d/m/Y H:i", strtotime($value));
					
					
				} else {
					$json[$property] = $value;
				}
			}
			
			return $json;
		}
		
		/**
		 * Get annotations
		 * @param $property Property name
		 */
		private function getAnnotations($property) {
			$c = new ReflectionProperty(get_class($this), $property);
			$s = $c->getDocComment();        
			$s = str_replace('/*', '', $s);
			$s = str_replace('*/', '', $s);
			$s = str_replace('*', '', $s);
			$aTags = explode('@', $s);
	
			return $aTags;
		}
	}
?>