<?php if (!defined('APPLICATION')) exit();

class UmlautMentionsFormatter {
   /**
    *  replaces /library/core/functions.general.php function GetMentions
    *  code is as close to original function (taken from 2.0.18.9) as possible 
    */
   public function GetMentions($String) {
      // This one grabs mentions that start at the beginning of $String
      // without spaces
      $StrippedValidationRegex = '['.str_replace(' ', '', str_replace('\s', '', C('Garden.User.ValidationRegex', '\d\w_'))).']'.C('Garden.User.ValidationLength','{3,20}');
      preg_match_all(
      // '/(?:^|[\s,\.>])@(\w{3,20})\b/i',
         '/(?:^|[\s,\.>])@('.$StrippedValidationRegex.')\b/i',
         $String,
         $MatchesStripped
      );
      // with spaces
      preg_match_all(
         '/(?:^|[\s,\.>])@'.C('Plugins.MentionsPlus.MentionStart', '"').'('.ValidateUsernameRegex().')'.C('Plugins.MentionsPlus.MentionStop', '"').'/i',
         $String,
         $Matches
      );

      $Result = array();
      // results without spaces
      if (count($MatchesStripped) > 1) {
         $Result = $MatchesStripped[1];
      }
      // merge results with spaces
      if (count($Matches) > 1) {
         $Result = array_merge($Result, $Matches[1]);
      }
      return array_unique($Result);
   }

   /**
    *  replaces /library/core/class.format.php function Mention
    *  code is as close to original function (taken from 2.0.18.9) as possible 
    */
   public function FormatMentions($Mixed) {
      if (!is_string($Mixed)) {
         return Gdn_Format::To($Mixed, 'Mentions');
      }

      // Handle @mentions.
      if(C('Garden.Format.Mentions')) {
         // without spaces
         $StrippedValidationRegex = '['.str_replace(' ', '', str_replace('\s', '', C('Garden.User.ValidationRegex'))).']'.C('Garden.User.ValidationLength','{3,20}');
         $Mixed = preg_replace(
            '/(^|[\s,\.>])@('.$StrippedValidationRegex.')\b/i',
            '\1'.Anchor('@\2', '/profile/\\2'),
            $Mixed
         );

         // with spaces
         $Mixed = preg_replace(
            '/(^|[\s,\.>])@'.C('Plugins.MentionsPlus.MentionStart', '"').'('.ValidateUsernameRegex().')'.C('Plugins.MentionsPlus.MentionStop', '"').'/i',
            '\1'.Anchor('@'.C('Plugins.MentionsPlus.MentionStart', '"').'\2'.C('Plugins.MentionsPlus.MentionStop', '"'), '/profile/\\2'),
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
            '/(^|[\n])(\\'.C('Plugins.MentionsPlus.MeActionCode', '/me').')(\s[^(\n)]+)/i',
            '\1'.Wrap(Wrap('\2', 'span', array('class' => 'MeActionName')).'\3', 'span', array('class' => 'AuthorAction')),
            $Mixed
         );
      }
      return $Mixed;
   }
}
