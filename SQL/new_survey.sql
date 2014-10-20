/*** insert creates survey ***/

SET @sid = 999; -- survey ID - IN int(11)
SET @ownerId = 1; -- owner_id - IN int(11)
SET @ownerName = 'Administrator'; -- owner name - IN varchar(50)
SET @ownerEmail = 'admin@admin.pl'; -- owner email - IN varchar(254)

SET @title = 'baza3';-- surveyls_title - IN varchar(200)
SET @description  = 'test baza3'; -- surveyls_description - IN text
SET @welcomeText = 'Hello Test01'; -- surveyls_welcometext - IN text


INSERT INTO lime_surveys (
   sid
  ,owner_id
  ,admin
  ,active
  ,expires
  ,startdate
  ,adminemail
  ,anonymized
  ,faxto
  ,format
  ,savetimings
  ,template
  ,language
  ,additional_languages
  ,datestamp
  ,usecookie
  ,allowregister
  ,allowsave
  ,autonumber_start
  ,autoredirect
  ,allowprev
  ,printanswers
  ,ipaddr
  ,refurl
  ,datecreated
  ,publicstatistics
  ,publicgraphs
  ,listpublic
  ,htmlemail
  ,sendconfirmation
  ,tokenanswerspersistence
  ,assessments
  ,usecaptcha
  ,usetokens
  ,bounce_email
  ,attributedescriptions
  ,emailresponseto
  ,emailnotificationto
  ,tokenlength
  ,showxquestions
  ,showgroupinfo
  ,shownoanswer
  ,showqnumcode
  ,bouncetime
  ,bounceprocessing
  ,bounceaccounttype
  ,bounceaccounthost
  ,bounceaccountpass
  ,bounceaccountencryption
  ,bounceaccountuser
  ,showwelcome
  ,showprogress
  ,questionindex
  ,navigationdelay
  ,nokeyboard
  ,alloweditaftercompletion
  ,googleanalyticsstyle
  ,googleanalyticsapikey
) VALUES (
   @sid -- sid - IN int(11)
  ,@ownerId -- owner_id - IN int(11)
  ,@ownerName  -- admin - IN varchar(50)
  ,'N' -- active - IN varchar(1)
  ,NULL  -- expires - IN datetime
  ,NULL  -- startdate - IN datetime
  ,@ownerEmail  -- adminemail - IN varchar(254)
  ,'N' -- anonymized - IN varchar(1)
  ,''  -- faxto - IN varchar(20)
  ,'G'  -- format - IN varchar(1)
  ,'Y' -- savetimings - IN varchar(1)
  ,'default'  -- template - IN varchar(100)
  ,'pl'  -- language - IN varchar(50)
  ,''  -- additional_languages - IN varchar(255)
  ,'Y' -- datestamp - IN varchar(1)
  ,'N'-- usecookie - IN varchar(1)
  ,'N' -- allowregister - IN varchar(1)
  ,'N' -- allowsave - IN varchar(1)
  ,'150' -- autonumber_start - IN int(11)
  ,'N' -- autoredirect - IN varchar(1)
  ,'Y' -- allowprev - IN varchar(1)
  ,'N' -- printanswers - IN varchar(1)
  ,'N' -- ipaddr - IN varchar(1)
  ,'N' -- refurl - IN varchar(1)
  ,CURRENT_DATE()  -- datecreated - IN date
  ,'N' -- publicstatistics - IN varchar(1)
  ,'N' -- publicgraphs - IN varchar(1)
  ,'N' -- listpublic - IN varchar(1)
  ,'Y' -- htmlemail - IN varchar(1)
  ,'N' -- sendconfirmation - IN varchar(1)
  ,'Y' -- tokenanswerspersistence - IN varchar(1)
  ,'N' -- assessments - IN varchar(1)
  ,'D' -- usecaptcha - IN varchar(1)
  ,'N' -- usetokens - IN varchar(1)
  ,''  -- bounce_email - IN varchar(254)
  ,'a:3:{s:11:"attribute_1";a:4:{s:11:"description";s:10:"konsultant";s:9:"mandatory";s:1:"N";s:13:"show_register";s:1:"N";s:7:"cpdbmap";s:0:"";}s:11:"attribute_2";a:4:{s:11:"description";s:15:"data rezerwacji";s:9:"mandatory";s:1:"N";s:13:"show_register";s:1:"N";s:7:"cpdbmap";s:0:"";}s:11:"attribute_3";a:4:{s:11:"description";s:14:"numer telefonu";s:9:"mandatory";s:1:"N";s:13:"show_register";s:1:"N";s:7:"cpdbmap";s:0:"";}}'  -- attributedescriptions - IN text
  ,''  -- emailresponseto - IN text
  ,''  -- emailnotificationto - IN text
  ,15 -- tokenlength - IN int(11)
  ,'Y'  -- showxquestions - IN varchar(1)
  ,'B'  -- showgroupinfo - IN varchar(1)
  ,'Y'  -- shownoanswer - IN varchar(1)
  ,'X'  -- showqnumcode - IN varchar(1)
  ,0   -- bouncetime - IN int(11)
  ,'N'  -- bounceprocessing - IN varchar(1)
  ,''  -- bounceaccounttype - IN varchar(4)
  ,''  -- bounceaccounthost - IN varchar(200)
  ,''  -- bounceaccountpass - IN varchar(100)
  ,''  -- bounceaccountencryption - IN varchar(3)
  ,''  -- bounceaccountuser - IN varchar(200)
  ,'N'  -- showwelcome - IN varchar(1)
  ,'Y'  -- showprogress - IN varchar(1)
  ,0 -- questionindex - IN int(11)
  ,0 -- navigationdelay - IN int(11)
  ,'N'  -- nokeyboard - IN varchar(1)
  ,'Y'  -- alloweditaftercompletion - IN varchar(1)
  ,'0'  -- googleanalyticsstyle - IN varchar(1)
  ,''  -- googleanalyticsapikey - IN varchar(25)
);



/*** insert to lang settings table ***/
INSERT INTO lime_surveys_languagesettings(
   surveyls_survey_id
  ,surveyls_language
  ,surveyls_title
  ,surveyls_description
  ,surveyls_welcometext
  ,surveyls_endtext
  ,surveyls_url
  ,surveyls_urldescription
  ,surveyls_email_invite_subj
  ,surveyls_email_invite
  ,surveyls_email_remind_subj
  ,surveyls_email_remind
  ,surveyls_email_register_subj
  ,surveyls_email_register
  ,surveyls_email_confirm_subj
  ,surveyls_email_confirm
  ,surveyls_dateformat
  ,surveyls_attributecaptions
  ,email_admin_notification_subj
  ,email_admin_notification
  ,email_admin_responses_subj
  ,email_admin_responses
  ,surveyls_numberformat
  ,attachments
) VALUES (
   @sid -- surveyls_survey_id - IN int(11)
  ,'pl' -- surveyls_language - IN varchar(45)
  ,@title -- surveyls_title - IN varchar(200)
  ,@description  -- surveyls_description - IN text
  ,@welcomeText  -- surveyls_welcometext - IN text
  ,''  -- surveyls_endtext - IN text
  ,''  -- surveyls_url - IN text
  ,''  -- surveyls_urldescription - IN varchar(255)
  ,''  -- surveyls_email_invite_subj - IN varchar(255)
  ,''  -- surveyls_email_invite - IN text
  ,''  -- surveyls_email_remind_subj - IN varchar(255)
  ,''  -- surveyls_email_remind - IN text
  ,''  -- surveyls_email_register_subj - IN varchar(255)
  ,''  -- surveyls_email_register - IN text
  ,''  -- surveyls_email_confirm_subj - IN varchar(255)
  ,''  -- surveyls_email_confirm - IN text
  ,1 -- surveyls_dateformat - IN int(11)
  ,'{"attribute_1":"konsultant","attribute_2":"data rezerwacji","attribute_3":"numer telefonu"}'  -- surveyls_attributecaptions - IN text
  ,''  -- email_admin_notification_subj - IN varchar(255)
  ,''  -- email_admin_notification - IN text
  ,''  -- email_admin_responses_subj - IN varchar(255)
  ,''  -- email_admin_responses - IN text
  ,1 -- surveyls_numberformat - IN int(11)
  ,''  -- attachments - IN text
);


/*** insert default question groups ***/
INSERT INTO lime_groups(
  sid
  ,group_name
  ,group_order
  ,description
  ,language
  ,randomization_group
  ,grelevance
) VALUES (
  @sid -- sid - IN int(11)
  ,'Pole przed rozmową' -- group_name - IN varchar(100)
  ,0 -- group_order - IN int(11)
  ,''  -- description - IN text
  ,'pl' -- language - IN varchar(20)
  ,'' -- randomization_group - IN varchar(20)
  ,''  -- grelevance - IN text
);

-- get inserted gid
SELECT @gid := gid FROM lime_groups WHERE sid = @sid;

/*** insert default questions ***/
INSERT INTO lime_questions(
   parent_qid
  ,sid
  ,gid
  ,type
  ,title
  ,question
  ,preg
  ,help
  ,other
  ,mandatory
  ,question_order
  ,language
  ,scale_id
  ,same_default
  ,relevance
) VALUES 
(0, @sid, @gid, 'D', 'CC00', 'Data kontaktu', '', '', 'N', 'Y', 1, 'pl', 0, 0, '1'),
(0, @sid, @gid, 'L', 'CC04', 'Status rozmowy', '', '', 'Y', 'Y', 3, 'pl', 0, 0, '1'),
(0, @sid, @gid, 'L', 'CC07', 'Która próba dotarcia?', '', '', 'Y', 'Y', 2, 'pl', 0, 0, '1'),
(0, @sid, @gid, 'X', 'info1', '<strong>Imię:</strong> {TOKEN:FIRSTNAME}<br /> <strong>Nazwisko</strong>: {TOKEN:LASTNAME}<br /> <strong>Telefon</strong>:&nbsp;<a href="sip:{TOKEN:ATTRIBUTE_3}">{TOKEN:ATTRIBUTE_3}</a><br />' , '', '', 'N', 'N', 0, 'pl', 0, 0, '1'),
(0, @sid, @gid, 'S', 'KONS', 'Konsultant', '', '', 'N', 'N', 5, 'pl', 0, 0, '1');

-- get qid of status question 
SELECT @statusQid := qid FROM lime_questions WHERE gid = @gid AND title = 'CC04';

-- insert question with relevance. this question is dependent on status answer
INSERT INTO lime_questions(
   parent_qid  ,sid  ,gid  ,type  ,title  ,question  ,preg  ,help  ,other  ,mandatory  ,question_order  ,language  ,scale_id  ,same_default  ,relevance
) VALUES 
(0, @sid, @gid, 'D', 'CC11', 'Na kiedy przełożona?', '', '', 'N', 'Y', 4, 'pl', 0, 0, CONCAT('((', @sid, 'X', @gid, 'X', @statusQid, '.NAOK == "A2"))'));

-- get qid of attempt question 
SELECT @attemptQid := qid FROM lime_questions WHERE gid = @gid AND title = 'CC07';


/*** insert answers ***/
INSERT INTO lime_answers(
   qid
  ,code
  ,answer
  ,sortorder
  ,assessment_value
  ,language
  ,scale_id
) VALUES 
(@statusQid, 'A1', 'Przeprowadzona', 1, 0, 'pl', 0),
(@statusQid, 'A2', 'Inny termin', 2, 1, 'pl', 0),
(@statusQid, 'A3', 'Nie odbiera', 3, 1, 'pl', 0),
(@statusQid, 'A4', 'Błędny numer', 4, 1, 'pl', 0),
(@statusQid, 'A5', 'Nie dzwonić', 5, 1, 'pl', 0),
(@statusQid, 'A6', 'Zajęty', 6, 1, 'pl', 0),
(@statusQid, 'A7', 'Numer dobry, brak klienta', 7, 1, 'pl', 0),
(@statusQid, 'A8', 'Poczta głosowa', 8, 1, 'pl', 0),
(@attemptQid, 'A1', 'Pierwsza', 1, 0, 'pl', 0),
(@attemptQid, 'A2', 'Druga', 2, 1, 'pl', 0),
(@attemptQid, 'A3', 'Trzecia', 3, 1, 'pl', 0);


-- get qid of contact date question and reschedule date question
SELECT @contactDateQid := qid FROM lime_questions WHERE gid = @gid AND title = 'CC00';
SELECT @rescheduleDateQid := qid FROM lime_questions WHERE gid = @gid AND title = 'CC11';

/*** insert date formats ***/
INSERT INTO lime_question_attributes(
   qid
  ,attribute
  ,value
) VALUES 
(@contactDateQid, 'date_format', 'dd.mm.yyyy HH:MM'),
(@rescheduleDateQid, 'date_format', 'dd.mm.yyyy HH:MM')

