<?php

class UmlautMentionsFormatter {
   public function GetMentions($String) {
decho('MyGetMentions_class.umlautmentionsformatter.php');   
      $Mentions = array();

      // This one grabs mentions that start at the beginning of $String
      preg_match_all(
         '/(?:^|[\s,\.>])@('.ValidateUsernameRegex().')\b/iu',
//         '/(?:^|[\s,\.>])@'.C('Plugins.MentionsPlus.MentionStart').'('.ValidateUsernameRegex().')'.C('Plugins.MentionsPlus.MentionStop').'\b/i',
         $String,
         $Matches
      );
      if (count($Matches) > 1) {
         $Result = array_unique($Matches[1]);
         return $Result;
      }
      return array();
   }
   
   public function FormatMentions($Mixed) {
      if (!is_string($Mixed)) {
         return Gdn_Format::To($Mixed, 'Mentions');
      }

      // Handle @mentions.
      if(C('Garden.Format.Mentions')) {

         $StrippedValidationRegex = '['.str_replace(' ', '', str_replace('\s', '', C('Garden.User.ValidationRegex'))).']'.C('Garden.User.ValidationLength','{3,20}');
decho('/(^|[\s,\.>])@('.$StrippedValidationRegex.')\b/i');
         $Mixed = preg_replace(
            '/(^|[\s,\.>])@('.$StrippedValidationRegex.')\b/i',
            '\1'.Anchor('@\2', '/profile/\\2'),
            $Mixed
         );
      
decho('/(^|[\s,\.>])@ß('.ValidateUsernameRegex().')ß/i');
         $Mixed = preg_replace(
            '/(^|[\s,\.>])@ß('.ValidateUsernameRegex().')ß/i',
            '\1'.Anchor('@ß\2ß', '/profile/\\2'),
            $Mixed
         );

      }

      
      // Handle #hashtag searches
      if(C('Garden.Format.Hashtags')) {
         $Mixed = preg_replace(
            '/(^|[\s,\.>])\#([\w\-]+)(?=[\s,\.!?]|$)/i',
            '\1'.Anchor('#\2', '/search?Search=%23\2&Mode=like').'\3',
            $Mixed
         );
      }
      // Handle "/me does x" action statements
      if(C('Garden.Format.MeActions')) {
         $Mixed = preg_replace(
            '/(^|[\n])(\/me)(\s[^(\n)]+)/i',
            '\1'.Wrap(Wrap('\2', 'span', array('class' => 'MeActionName')).'\3', 'span', array('class' => 'AuthorAction')),
            $Mixed
         );
      }
    
      return $Mixed;
   }
}
