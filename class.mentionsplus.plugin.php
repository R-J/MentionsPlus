<?php if (!defined('APPLICATION')) exit();

$PluginInfo['MentionsPlus'] = array(
   'Name' => 'Mentions+',
   'Description' => 'Mentions+ allows usage of nearly arbitrary non space characters in mentions. Mentions will use every character that is allowed for registration.',
   'Version' => '0.1',
   'RequiredApplications' => array('Vanilla' => '>=2.0.18'),
   'SettingsUrl' => '/settings/mentionsplus',
   'SettingsPermission' => 'Garden.Moderation.Manage',
   'HasLocale' => FALSE,
   'Author' => 'Robin',
   'License' => 'GNU GPLv2',
   'MobileFriendly' => TRUE
);

class MentionsPlusPlugin extends Gdn_Plugin {
   public function Setup() {
    // Set config settings only if they are not already set
      if (!C('Garden.User.ValidationRegex')) {
         SaveToConfig('Garden.User.ValidationRegex', '\d\w_ äöüß'); // special German characters for testing
      }
      if (!C('Garden.User.ValidationLength')) {
         SaveToConfig('Garden.User.ValidationLength', '{3,20}');
      }
      if (!C('Plugins.MentionsPlus.MentionStart')) {
         SaveToConfig('Plugins.MentionsPlus.MentionStart', '"');
      }
      if (!C('Plugins.MentionsPlus.MentionStop')) {
         SaveToConfig('Plugins.MentionsPlus.MentionStop', '"');
      }
   }

   public function __construct() {
      require_once(PATH_PLUGINS.DS.'MentionsPlus'.DS.'class.umlautmentionsformatter.php');
      Gdn::FactoryInstall('MentionsFormatter', 'UmlautMentionsFormatter', NULL, Gdn::FactoryInstance);
   }
   
   
   public function SettingsController_MentionsPlus_Create($Sender) {
      $Sender->Permission('Garden.Settings.Manage');
      $Sender->SetData('Title', T('Mentions+ Settings'));
      $Sender->AddSideMenu('dashboard/settings/plugins');

      $Conf = new ConfigurationModule($Sender);
      $Conf->Initialize(array(
         'Garden.User.ValidationRegex' => array(
            'LabelCode' => 'Regular expression that evaluates a valid username',
            'Control' => 'TextBox',
            'Default' => '\d\w_äöüß'
         ),
         'Garden.User.ValidationLength' => array(
            'LabelCode' => 'Min/max length for valid usernames (in regex notation)',
            'Control' => 'TextBox',
            'Default' => '{3,20}'
         ),
         'Plugins.MentionsPlus.MentionStart' => array(
            'LabelCode' => 'Beginning escape character',
            'Control' => 'TextBox',
            'Default' => '"',
            'Description' => T('SettingMentionStartDescription', 'If using whitespaces in usernames you have to mark what belongs to a username: @"hans wurst"')
         ),
         'Plugins.MentionsPlus.MentionStop' => array(
            'LabelCode' => 'Ending escape character',
            'Control' => 'TextBox',
            'Default' => '"',
            'Description' => T('SettingMentionStopDescription', 'If you would like to use different characters for escaping, you could set them separately: @<hans wurst>')
         )
      ));
      $Conf->RenderAll();
   }
}


