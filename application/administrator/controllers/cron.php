<?php
	class Cron extends CI_Controller
	{
		function __construct()
		{
			parent::__construct();
		}

		/**
		 * Import keywords for basic searching assistance
		 */
		function import_keyword($pass = '')
		{
			// So that we don't have time out
			set_time_limit(0);
			
			if($pass === 'pass786')
			{
				// Clear previous data
				$this->db->truncate('keyword');
				
				$fields = array(
					// Import unique Property fieldset
					'property' => array(
						'province',
						'city',
						'area',
						'code',
						'title_en',
						'title_de',
						'title_it',
						'title_ru'
				));


				foreach($fields as $table => $values)
				{
					foreach($values as $field)
					{
//						$sql = "INSERT INTO keyword (keyword, table_field, created)
//							SELECT DISTINCT $field, CONCAT('region_$field'), NOW() FROM $table WHERE $field != ''
//						";
//						$query = $this->db->query($sql);
//
//						echo "No. of imported rows from $table ($field) " . $this->db->affected_rows() . "<br/>";

						$sql = "SELECT DISTINCT $field as field FROM $table WHERE $field != ''";
						$query = $this->db->query($sql);
						$count = 0;
						foreach($query->result() as $row)
						{
							// Filter keyword, make sure there is only one
							$keyword = str_replace(array('/', '+', '-'), ' ', $row->field);
							
							if($this->db->get_where('keyword', array('keyword' => $keyword))->num_rows() === 0)
							{
								$sql = "INSERT INTO keyword (keyword, table_field, created) VALUES ('$keyword', '" . $table ."_" . $field. "', NOW())";
								$this->db->query($sql);
								$count++;
							}
						}

						echo "No. of imported rows from $table ($field) " . $count . "<br/>";
					}					
				}								
			}
		}
	}