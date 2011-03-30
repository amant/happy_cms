<?php
	class Import_db extends CI_Controller
	{
		function __construct()
		{
			parent::__construct();
		}

		function _cleanUpHTML($text) {

			// remove escape slashes
			$text = stripslashes($text);

			// trim everything before the body tag right away, leaving possibility for body attributes
			//$text = stristr( $text, '<body');

			// strip tags, still leaving attributes, second variable is allowable tags
			$text = strip_tags($text, '<p><b><i><u><a><h1><h2><h3><h4><h4><h5><h6>');

			// removes the attributes for allowed tags, use separate replace for heading tags since a
			// heading tag is two characters
			$text = preg_replace('/<([p|b|i|u])[^>]*>/', '<${1}>', $text);
			$text = preg_replace('/<([h1|h2|h3|h4|h5|h6][1-6])[^>]*>/', '<${1}>', $text);

			return ($text);

		}

		/**
		 * Insert CSV or XML extracted data to DB
		 *
		 * @param array $contents
		 * @param array $map_data
		 * @param array $data
		 * @param string $table
		 */
		private function _insert_data($contents, array $map_data, array $table_data, $table)
		{
			// Iterate through the contents
			foreach ($contents as $content)
			{
				$data    = $table_data;

				// Insert field to data which matches the key
				foreach ($content as $key => $field)
				{
					if (array_key_exists($map_data[$key], $data))
					{
						$data[$map_data[$key]] = $field;
					}
				}

				echo "<pre>";
				print_r($data);
				echo "</pre>";

				// Insert to db
				$this->db->insert($table, $data);

				echo "<pre>";
				print_r($this->db->last_query());
				echo "</pre>";
			}
		}

		/**
		 * Read CSV file
		 *
		 * @param string $filename
		 * @return array
		 */
		private function _read_csv($filename)
		{
			$csv_data = array();
			$fields   = array();

			$row = 1;
			if (($handle = fopen($filename, "r")) !== FALSE)
			{
				while (($data = fgetcsv($handle, 9000, ",")) !== FALSE)
				{
					// First line contains field
					if ($row === 1)
					{
						$fields = $data;
					}
					else
					{
						$csv_data[] = array_combine($fields, $data);
					}

					$row++;
				}
				fclose($handle);
			}
			return $csv_data;
		}

		private function _xml2array($fname)
		{
			$sxi = new SimpleXmlIterator($fname, null, true);
			return $this->_sxiToArray($sxi);
		}

		private function _sxiToArray($sxi)
		{
			$a = array();
			for ($sxi->rewind(); $sxi->valid(); $sxi->next())
			{
				if (!array_key_exists($sxi->key(), $a))
				{
					$a[$sxi->key()] = array();
				}
				if ($sxi->hasChildren())
				{
					$a[$sxi->key()][] = $this->_sxiToArray($sxi->current());
				}
				else
				{
					$a[$sxi->key()] = strval($sxi->current());
				}
			}
			return $a;
		}

		function import_picture_to_property()
		{
			$properties = $this->db->get('property');
			foreach($properties->result() as $property)
			{
				$picture = $this->db->get_where('property_picture', array('property_id' => $property->id));
				if($picture->num_rows() > 0)
				{
					$picture = $picture->row();					
					$this->db->update('property', array('image' => $picture->image), array('id' => $property->id));

					echo $this->db->last_query();
					echo '<br/>';

					// copy the image both thumbnail size and big one
					$path = './public/images/';
					copy($path . 'property_picture/thumb/' . $picture->image, $path . 'property/thumb/' . $picture->image);
					copy($path . 'property_picture/big/' . $picture->image, $path . 'property/big/' . $picture->image);
				}
			}
		}


		function import_availability()
		{
			$filename = './temp/Calendar.csv';
			$contents = $this->_read_csv($filename);

			foreach($contents as $content)
			{
				$type_id = 3;
				if($content['CAL_REN_ID'] == 'Closed')
				{
					$type_id = 4;
				}

				$cal_date = explode(' ', $content['CAL_Date']);
				$date = explode('/', $cal_date[0]);

				$data = array(
					'availability_type_id' => $type_id,
					'property_id' => $content['CAL_REN_ID'],
					'date' => $date[2] . '-' . $date[0] . '-' . $date[1]
				);

				$this->db->insert('property_availability', $data);
			}

			echo 'Done';
		}

		function import_extra_charge()
		{
			$filename = './temp/RentAddPrice.xml';
			$contents = $this->_xml2array($filename);

			$count = 0;
			foreach($contents['Table'] as $content)
			{
				$id = $content['ADDPRI_ID'];
				$property_id = $content['ADDPRI_REN_ID'];

				unset($content['ADDPRI_ID']);
				unset($content['ADDPRI_REN_ID']);

				$type_id = 1;
				foreach($content as $key => $value)
				{
					$price = 0;					
					$mandatory = 0;
					$interval = 4;

					if($value == '-1'){
						$mandatory = 7;
					}
					else if($value == '-2')
					{
						$interval = 3;
					}
					else
					{
						$price = $value;
					}

					$data = array(
						'id' => $id,
						'property_id' => $property_id,
						'extra_charge_type_id' => $type_id,
						'interval' => $interval,
						'mandatory' => $mandatory,
						'pay_at_location' => '',
						'pay_per_order' => '',
						'price' => $price
					);

					print_r($data);
					$this->db->insert('property_extra_charge', $data);

					$type_id++;
				}
			}
			echo 'done';
		}

		function import_price()
		{
			$filename = './temp/RentalPrice.csv';
			$contents = $this->_read_csv($filename);

			foreach($contents as $count => $content)
			{
				foreach($content as $key => $value){
					if($key === 'RENPRI_From' || $key === 'RENPRI_To')
					{
						$date = explode(' ', $value);
						$new_date = explode('/', $date[0]);
						$contents[$count][$key] = $new_date[2] . '-' . $new_date[0] . '-' . $new_date[1];						
					}

					if($key === 'RENPRI_PerPerson')
					{
						if($value == 'false')
						{
							$contents[$count][$key] = 0;
						}
						else
						{
							$contents[$count][$key] = 1;
						}
					}
				}
			}

			$map_data = array(
				'RENPRI_REN_ID' => 'property_id',
				'RENPRI_From' => 'start_date',
				'RENPRI_To' => 'end_date',
				'RENPRI_Price' => 'price',
				'RENPRI_People' => 'person',
				'RENPRI_PerPerson' => 'is_per_person',
				'RENPRI_AddPeople' => 'additional_person_price',
				'RENPRI_Reduction' => 'reduction_price',
				'RENPRI_OtherPrice' => 'other_price',
				'RENPRI_Type' => 'price_type_id'
			);

			$data = array(
				'id' => '',
				'property_id' => '',
				'price_type_id' => '',
				'start_date' => '',
				'end_date' => '',
				'price' => '',
				'is_per_person' => '',
				'person' => '',
				'additional_person_price' => '',
				'reduction_price' => '',
				'other_price' => ''
			);

			$table = 'property_price';

			$this->_insert_data($contents, $map_data, $data, $table);
		}

		function import_attribute()
		{
			$filename = './temp/rental.xml';
			$contents = $this->_xml2array($filename);

			foreach($contents as $content){
				foreach($content as $key => $row)
				{
					$secondary = str_replace('#', '', $row['REN_SecDetails']);
					$attributes = explode('-', $secondary);

					foreach($attributes as $attribute_id){

						$map = array(
							1 => 104,
							2 => 93,
							6 => 10,
							7 => 144,
							10 => 97,
							12 => 54,
							13 => 101,
							14 => 129,
							15 => 78,
							16 => 8,
							17 => 118,
							18 => 143,
							19 => 100,
							20 => 98,
							21 => 62,
							22 => 145,
							25 => 67,
							26 => 83,
							27 => 84
						);


						if(isset($map[$attribute_id])){
							$data = array(
								'property_id' => $row['REN_ID'],
								'attribute_id' => $map[$attribute_id]
							);

							// Max pet
							if($map[$attribute_id] == 8)
							{
								$data['value'] = 1;
							}

//							echo '<pre>';
//							print_r($attribute_id);
//							print_r($data);
//							echo '</pre>';

							if($this->db->get_where('property_attribute', $data)->num_rows() <= 0)
							{
								$this->db->insert('property_attribute', $data);
							}
							
						}
					}
				}
			}
		}

		function import_property()
		{
			$filename = './temp/rental.xml';
			$contents = $this->_xml2array($filename);

			$map_data = array(
				'REN_ID' => 'id',
				'REN_AGE_ID' => 'client_id',
				'REN_ProName' => 'title_en',
				'REN_ProType' => 'property_type_id',
				'REN_Rank' => 'rank',
				'REN_CNT_ID' => 'rank',
				'REN_REG_ID' => 'region_id',
				'REN_OnRequest' => '',
				'REN_Comm' => '',
				'REN_CommType' => '',
				'REN_Province' => 'province',
				'REN_City' => 'city',
				'REN_Area' => 'area',
				'REN_Status' => '',
				'REN_Located' => '',
				'REN_FloorSpace' => 'floor_space',
				'REN_Land' => 'land_space',
				'REN_Room' => 'number_of_room',
				'REN_Bathroom' => 'number_of_bathroom',
				'REN_SleepFrom' => 'person_min',
				'REN_SleepTo' => 'person_max',
				'REN_SwimingPool' => '',
				'REN_Pets' => '',
				'REN_SecDetails' => '',
				'REN_Description_Eng' => 'description_en_1',
				'REN_Description_Ger' => 'description_de_1',
				'REN_Description_Ita' => 'description_it_1',
				'REN_Description_Russ' => 'description_ru_1',
				'REN_Visibility' => 'status',
				'REN_InternalInfo' => 'code',
				'REN_Name' => '',
				'REN_Address' => '',
				'REN_Phone' => '',
				'REN_Fax' => '',
				'REN_Email' => '',
				'REN_Note' => '',
				'REN_DisplayPriceType' => ''
			);

			$data = array(
				'id' => '',
				'property_type_id' => '',
				'client_id' => '',
				'country_id' => '',
				'region_id' => '',
				'terms_id' => '',
				'booking_template_id' => '',
				'metakey' => '',
				'metadesc' => '',
				'metadata' => '',
				'province' => '',
				'city' => '',
				'area' => '',
				'floor_space' => '',
				'land_space' => '',
				'number_of_room' => '',
				'number_of_bathroom' => '',
				'person_max' => '',
				'person_min' => '',
				'children_max' => '',
				'stay_min' => '',
				'title_en' => '',
				'description_en_1' => '',
				'description_en_2' => '',
				'description_en_3' => '',
				'title_de' => '',
				'description_de_1' => '',
				'description_de_2' => '',
				'description_de_3' => '',
				'title_it' => '',
				'description_it_1' => '',
				'description_it_2' => '',
				'description_it_3' => '',
				'title_ru' => '',
				'description_ru_1' => '',
				'description_ru_2' => '',
				'description_ru_3' => '',
				'image' => '',
				'code' => '',
				'note' => '',
				'hits' => '',
				'rank' => '',
				'longitude' => '',
				'latitude' => '',
				'arrival_data' => '',
				'created' => gmdate('Y-m-d'),
				'created_by' => 1,
				'modified' => gmdate('Y-m-d'),
				'modified_by' => 1
			);

			$table = 'property';

			$this->_insert_data($contents['Table'], $map_data, $data, $table);
		}

		function import_property_correction()
		{
			$query = $this->db->get('property');
			foreach($query->result() as $row)
			{
				$description_en = strip_tags($row->description_en_1, '<a><p>');
				$description_de = strip_tags($row->description_de_1);
				$description_it = strip_tags($row->description_it_1);
				$description_ru = strip_tags($row->description_ru_1);

				$this->db->where('id', $row->id);
				$this->db->update('property', array(
						'description_en_1' => $description_en,
						'description_de_1' => $description_de,
						'description_it_1' => $description_it,
						'description_ru_1' => $description_ru,
				));
				echo $row->id . '<br/>';
			}
		}

		function import_region()
		{
			$filename = './temp/Region.xml';
			$contents = $this->_xml2array($filename);

			$map_data = array(
				'REG_ID' => 'id',
				'REG_CNT_ID' => 'country_id',
				'REG_EngName' => 'title_en',
				'REG_GerName' => 'title_de',
				'REG_ItaName' => 'title_it',
				'REG_RusName' => 'title_ru',
				'REG_Link' => 'link',
				'REG_Image' => 'image',
				'REG_EngDesc' => 'description_en',
				'REG_GerDesc' => 'description_de',
				'REG_ItaDesc' => 'description_it',
				'REG_RusDesc' => 'description_ru'
			);

			$data = array(
				'id' => '',
				'country_id' => '',
				'title_en' => '',
				'title_de' => '',
				'title_it' => '',
				'title_ru' => '',
				'description_en' => '',
				'description_de' => '',
				'description_it' => '',
				'description_ru' => '',
				'image' => '',
				'status' => '1',
				'created' => gmdate('Y-m-d'),
				'created_by' => 1,
				'modified' => gmdate('Y-m-d'),
				'modified_by' => 1
			);

			$table = 'region';

			$this->_insert_data($contents['Table'], $map_data, $data, $table);
		}

		function import_property_type()
		{
			$filename = './temp/PropertyType.csv';
			$contents = $this->_read_csv($filename);

			$map_data = array(
				'PROTYPE_ID' => 'id',
				'PROTYPE_EngName' => 'title_en',
				'PROTYPE_GerName' => 'title_de',
				'PROTYPE_ItaName' => 'title_it',
				'PROTYPE_RusName' => 'title_ru'
			);

			$data = array(
				'id' => '',
				'title_en' => '',
				'title_de' => '',
				'title_it' => '',
				'title_ru' => '',
				'title_fr' => '',
				'status' => '',
				'created' => gmdate('Y-m-d'),
				'created_by' => 1,
				'modified' => gmdate('Y-m-d'),
				'modified_by' => 1
			);

			$table = 'property_type';

			$this->_insert_data($contents, $map_data, $data, $table);
		}

		function import_agency()
		{
			$contents = simplexml_load_file('./temp/Mas_Agency.xml');

			$map_data = array(
				'AGE_ID' => 'id',
				'AGE_Name' => 'company',
				'AGE_BankDetail' => 'bank_detail',
				'AGE_PaymentDetail' => 'payment_detail',
				'AGE_Address' => 'address',
				'AGE_Contact' => 'contact'
			);

			$data = array(
				'id' => '',
				'client_type_id' => '1',
				'f_name' => '',
				'm_name' => '',
				'l_name' => '',
				'image' => '',
				'country' => '',
				'address' => '',
				'phone' => '',
				'fax' => '',
				'email' => '',
				'company' => '',
				'note' => '',
				'contact' => '',
				'bank_detail' => '',
				'payment_detail' => '',
				'payment_detail' => '',
				'status' => '1',
				'created' => gmdate('Y-m-d'),
				'created_by' => 1,
				'modified' => gmdate('Y-m-d'),
				'modified_by' => 1
			);

			$table = 'client';

			$this->_insert_data($contents, $map_data, $data, $table);
		}

		function import_rental()
		{
			$filename = './temp/Rental.csv';
			$csv_data = $this->_read_csv($filename);

			$map = array(
				'REN_ID' => 'id',
				'REN_Ref_No' => 'code',
				'REN_AGE_ID' => '',
				'REN_ProName' => 'title_en',
				'REN_ProType' => 'property_type_id',
				'REN_Rank' => 'rank',
				'REN_CNT_ID' => '',
				'REN_REG_ID' => '',
				'REN_OnRequest' => 'price_on_request',
				'REN_Comm' => 'commision',
				'REN_CommType' => 'commision_type',
				'REN_Province' => 'province',
				'REN_City' => 'city',
				'REN_Area' => 'area',
				'REN_Status' => 'occupy_status',
				'REN_Located' => 'located',
				'REN_FloorSpace' => 'floor',
				'REN_Land' => 'land',
				'REN_Room' => 'number_of_room',
				'REN_Bathroom' => 'number_of_bathroom',
				'REN_SleepFrom' => 'sleep_min',
				'REN_SleepTo' => 'sleep_min',
				'REN_SwimingPool' => 'swimming_pool',
				'REN_Pets' => 'pets',
				'REN_SecDetails' => 'secondary_detail',
				'REN_Description_Eng' => 'description_en',
				'REN_Description_Ger' => 'description_ge',
				'REN_Description_Ita' => 'description_it',
				'REN_Description_Russ' => 'description_ru',
				'REN_Visibility' => 'status',
				'REN_InternalInfo' => 'internal_info',
				'REN_Name' => 'name',
				'REN_Address' => 'address',
				'REN_Phone' => 'phone',
				'REN_Fax' => 'fax',
				'REN_Email' => 'email',
				'REN_Note' => 'note',
				'REN_DisplayPriceType' => 'display_price_type'
			);

			foreach ($csv_data as $data)
			{
				$content = array(
					'id' => '',
					'property_type_id' => '',
					'client_id' => '',
					'province' => '',
					'city' => '',
					'area' => '',
					'floor_space' => '',
					'land_space' => '',
					'number_of_room' => '',
					'number_of_bathroom' => '',
					'sleep_max' => '',
					'sleep_min' => '',
					'pets' => '',
					'title_en' => '',
					'description_en_1' => '',
					'title_de_1' => '',
					'description_de_1' => '',
					'title_it_1' => '',
					'description_it_1' => '',
					'title_ru_1' => '',
					'description_ru_1' => '',
					'title_fr_1' => '',
					'description_fr_1' => '',
					'status' => '',
					'created' => gmdate('Y-m-d'),
					'created_by' => 1,
					'modified' => gmdate('Y-m-d'),
					'modified_by' => 1
				);

				foreach ($data as $key => $field)
				{
					$content[$map[$key]] = $field;
				}

				echo "<pre>";
				print_r($content);
				echo "</pre>";

				// Insert db
				//$this->db->insert('property', $content);
			}
		}

		function import_rental_xml()
		{
			$xml_data = simplexml_load_file('./temp/Rental.xml');

			$map = array(
				'REN_ID' => 'id',
				'REN_Ref_No' => 'code',
				'REN_AGE_ID' => '',
				'REN_ProName' => 'title_en',
				'REN_ProType' => 'property_type_id',
				'REN_Rank' => 'rank',
				'REN_CNT_ID' => '',
				'REN_REG_ID' => '',
				'REN_OnRequest' => 'price_on_request',
				'REN_Comm' => 'commision',
				'REN_CommType' => 'commision_type',
				'REN_Province' => 'province',
				'REN_City' => 'city',
				'REN_Area' => 'area',
				'REN_Status' => 'occupy_status',
				'REN_Located' => 'located',
				'REN_FloorSpace' => 'floor',
				'REN_Land' => 'land',
				'REN_Room' => 'number_of_room',
				'REN_Bathroom' => 'number_of_bathroom',
				'REN_SleepFrom' => 'sleep_min',
				'REN_SleepTo' => 'sleep_min',
				'REN_SwimingPool' => 'swimming_pool',
				'REN_Pets' => 'pets',
				'REN_SecDetails' => 'secondary_detail',
				'REN_Description_Eng' => 'description_en',
				'REN_Description_Ger' => 'description_de',
				'REN_Description_Ita' => 'description_it',
				'REN_Description_Russ' => 'description_ru',
				'REN_Visibility' => 'status',
				'REN_InternalInfo' => 'internal_info',
				'REN_Name' => 'name',
				'REN_Address' => 'address',
				'REN_Phone' => 'phone',
				'REN_Fax' => 'fax',
				'REN_Email' => 'email',
				'REN_Note' => 'note',
				'REN_DisplayPriceType' => 'display_price_type'
			);

			$content = array(
				'id' => '',
				'property_type_id' => '',
				'client_id' => '',
				'province' => '',
				'city' => '',
				'area' => '',
				'floor_space' => '',
				'land_space' => '',
				'number_of_room' => '',
				'number_of_bathroom' => '',
				'sleep_max' => '',
				'sleep_min' => '',
				'pets' => '',
				'title_en' => '',
				'description_en_1' => '',
				'title_de_1' => '',
				'description_de_1' => '',
				'title_it_1' => '',
				'description_it_1' => '',
				'title_ru_1' => '',
				'description_ru_1' => '',
				'title_fr_1' => '',
				'description_fr_1' => '',
				'status' => '',
				'created' => gmdate('Y-m-d'),
				'created_by' => 1,
				'modified' => gmdate('Y-m-d'),
				'modified_by' => 1
			);
		}


		function import_homepage()
		{
			$filename = './temp/homepage.xml';
			$contents = $this->_xml2array($filename);

			$count = 0;
			$data = array();
			foreach($contents['Table'] as $content)
			{
				$col = trim($content['CONTENT_Link']);

				$img = explode('\\',$content['CONTENT_Image']);

				$data[$col]	= array(
					'image' => strtolower(url_title($img[1])),
					'cms_type_id' => 1,
					'title_en' => '',
					'fulltext_en' => '',
					'title_de' => '',
					'fulltext_de' => '',
					'title_it' => '',
					'fulltext_it' => '',
					'title_ru' => '',
					'fulltext_ru' => '',
					'created' => gmdate('Y-m-d'),
					'modified' => gmdate('Y-m-d'),
					'modified_by' => '1',
					'created_by' => '1',
					'status' => '1',
				);				
			}

			foreach($contents['Table'] as $content)
			{
				$col = trim($content['CONTENT_Link']);

				if($content['CONTENT_Lang'] == 'English')
				{
					$data[$col]['title_en'] = $content['CONTENT_Header'];
					$data[$col]['fulltext_en'] = $this->_cleanUpHTML($content['CONTENT_Desc']);
				}
				elseif($content['CONTENT_Lang'] == 'German')
				{
					$data[$col]['title_de'] = $content['CONTENT_Header'];
					$data[$col]['fulltext_de'] = $this->_cleanUpHTML($content['CONTENT_Desc']);
				}
				elseif($content['CONTENT_Lang'] == 'Italian')
				{
					$data[$col]['title_it'] = $content['CONTENT_Header'];
					$data[$col]['fulltext_it'] = $this->_cleanUpHTML($content['CONTENT_Desc']);
				}
				elseif($content['CONTENT_Lang'] == 'Russian')
				{
					$data[$col]['title_ru'] = $content['CONTENT_Header'];
					$data[$col]['fulltext_ru'] = $this->_cleanUpHTML($content['CONTENT_Desc']);
				}
			}

			foreach($data as $value){
				$this->db->insert('cms_content', $value);
			}
			
			
			echo 'done';
		}		
	}