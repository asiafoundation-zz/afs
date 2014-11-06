<?php 

class ImportExcelProcess {

    public function fire($job, $data)
    {
        $query = DB::insert('INSERT INTO queue (timestamp) VALUES(NOW())');
    	$status = 0;
        $survey = Survey::where('id', '=', $data['survey_id'])->first();
    	// save code
    	$codes = MasterCode::savingProcess($data['request']);
    	// Load Master Code Data
    	$master_code = MasterCode::loadData($data['request']);
    	// Load Excel Data
    	$excel_data = Survey::readHeader($survey->baseline_file, 'BZ', 1);
    	// Import Data
    	$status = Survey::importData($survey,$master_code,$excel_data);

    	$survey->publish = 3;
    	$survey->save();

        $job->delete();
    }

}