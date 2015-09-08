<?php

namespace Thunderhawk\API\Tag;

use Thunderhawk\API\Tag;
use Thunderhawk\API\Mvc\Model\Resultset;

class Table extends Tag{
	
	/**
	 * Generate HTML Table by Model ResultSet
	 * @param Model $model
	 */
	static public function generate(Resultset $resultset){
		$code = '<table>' ;
		$code .= '<thead></thead>' ;
		$code .= '<tbody>' ;
		foreach ($resultset as $record){
			$code .= '<tr>' ;
			foreach ($record->toArray() as $value){
				$code .= '<td>'.$value.'</td>' ;
			}
			$code .= '</tr>' ;
		}
		$code .= '</tbody>' ;
		$code .= '<tfoot></tfoot>' ;
		$code .= '</table>';
		
		return $code ;
	}
}