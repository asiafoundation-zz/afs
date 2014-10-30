<?php

class ParticipantSeeder extends Seeder {

  public function run()
  {
    DB::table('participants')->truncate();
    DB::table('question_participants')->truncate();
    DB::table('filter_participants')->truncate();

    // $participants = array();

    // for ($i=0; $i < 500; $i++) { 
    //   $participants[$i] = array('0');
    // }

    // Participant::truncate();

    // foreach ($participants as $key => $participant) {
    //   $participant_id = DB::table('participants')->insertGetId(
    //     array("sample_type" => $participant[0])
    //   );

    //   // Create question_participant
    //   $question_participants = array();

    //   $question_participants[0] = array($participant_id,rand(1,2),rand(1,34));
    //   $question_participants[1] = array($participant_id,rand(3,4),rand(1,34));
    //   $question_participants[2] = array($participant_id,rand(5,8),rand(1,34));
    //   $question_participants[3] = array($participant_id,rand(9,12),rand(1,34));
    //   $question_participants[4] = array($participant_id,rand(13,14),rand(1,34));
    //   $question_participants[5] = array($participant_id,rand(15,16),rand(1,34));

    //   foreach ($question_participants as $key => $question_participant) {
    //     QuestionParticipant::create(
    //       array(
    //         "participant_id" => $question_participant[0],
    //         "answer_id" => $question_participant[1],
    //         "region_id" => $question_participant[2],
    //       )
    //     );
    //   }

    //   // Create filter_participant
    //   $filter_participant = array();

    //   $filter_participants[0] = array(rand(1,3),$participant_id);
    //   $filter_participants[1] = array(rand(4,6),$participant_id);

    //   foreach ($filter_participants as $key => $filter_participant) {
    //     FilterParticipant::create(
    //       array(
    //       "category_item_id" => $filter_participant[0],
    //       "participant_id" => $filter_participant[1],
    //       )
    //     );
    //   }
    // }
  }
}