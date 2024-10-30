<?php 

/**
 * Output data for a custom column on the student reporting table/export
 * @param    string     $value    default value being output
 * @param    string     $key      name of the custom field being output
 * @param    obj        $student  LLMS_Student for the row
 * @param    string     $context  output context "display" or "export"
 * @return   mixed
 */
function my_students_table_column_data( $value, $key, $data, $context ) {
	
	if ( 'my_new_reg_menu' === $key ) {
		$value = '';
		$value_index = get_user_meta( $data->get_id(), $key, true );
		$dd_selection_names = get_option('dd_selection_name_list',array());
		foreach($dd_selection_names as $dd_selection_name){
			 if($dd_selection_name['id']==$value_index){
				$value = $dd_selection_name['name'] ;
			 }  
		}
	}
	return $value;

}
add_filter( 'llms_table_get_data_students', 'my_students_table_column_data', 10, 4 );

/**
 * Add custom columns to the main students reporting table
 * @param    [type]     $cols  [description]
 * @return   [type]
 * @since    [version]
 * @version  [version]
 */
function my_students_table_columns( $cols ) {

	$cols['my_new_reg_menu'] = array(
		'title' => __( 'College', '' ),
		'export_only' => true, // set to false to show display screens but include in exports
		'exportable' => true, // set to false to exclude from exports
	);

	return $cols;
}
add_filter( 'llms_table_get_students_columns', 'my_students_table_columns', 10 );