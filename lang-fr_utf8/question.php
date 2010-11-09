<?php // $Id: question.php,v 1.48 2009/11/25 17:54:30 martignoni Exp $

$string['adminreport'] = 'Rapport sur les problèmes possibles dans votre banque de questions.';
$string['availableq'] = 'Disponible ?';
$string['badbase'] = 'Mauvaise base avant **: $a**';
$string['broken'] = 'Ce lien est « cassé », il pointe vers un fichier inexistant.';
$string['byandon'] = 'par <em>$a->user</em> à <em>$a->time</em>';
$string['cannotcopybackup'] = 'Impossible de copier le fichier de sauvegarde';
$string['cannotcreate'] = 'Impossible de créer un nouvel enregistrement dans la table question_attempts';
$string['cannotcreatedataset'] = 'Impossible de créer le jeu de données $a';
$string['cannotcreatepath'] = 'Impossible de créer le chemin $a';
$string['cannotcreaterelation'] = 'Impossible de créer la relation vers le jeu de données $a[0] $a[1]';
$string['cannotdeletecate'] = 'Vous ne pouvez pas supprimer cette catégorie, car c\'est la catégorie par défaut de ce contexte.';
$string['cannotenable'] = 'Le type de question $a ne peut pas être créé directement.';
$string['cannotfindcate'] = 'Impossible de trouver l\'enregistrement pour la catégorie';
$string['cannotfindquestionfile'] = 'Impossible de trouver le fichier des données de question dans le fichier compressé';
$string['cannotgetdsfordependent'] = 'Impossible d\'obtenir le jeu de données indiqué pour une question dépendant d\'un jeu de données ! (question : {$a[0]}, élément : {a[1]})';
$string['cannotgetdsforquestion'] = 'Impossible d\'obtenir le jeu de données indiqué pour une question calculée ! (question : {$a})';
$string['cannothidequestion'] = 'Impossible de cacher la question';
$string['cannotimportformat'] = 'Désolé, l\'importation de ce format n\'est pas encore implémentée !';
$string['cannotinsert'] = 'Erreur : impossible d\'insérer l\'élément du jeu de données';
$string['cannotinsert'] = 'Impossible de créer un nouvel enregistrement dans question_sessions';
$string['cannotinsertitem'] = 'Impossible d\'insérer l\'élément du jeu de données $a[0] dans $a[1] pour $a[2]';
$string['cannotinsertquestion'] = 'Impossible d\'insérer une nouvelle question !';
$string['cannotinsertquestioncate'] = 'Impossible d\'insérer la nouvelle catégorie de questions $a';
$string['cannotinsertquestioncatecontext'] = 'Impossible d\'insérer la nouvelle catégorie de questions $a[0], identifiant de contexte illégal $a[1]';
$string['cannotloadquestion'] = 'Impossible de charger la question';
$string['cannotmovecate'] = 'Impossible de déplacer la catégorie $a. C\'est la dernière de ce contexte.';
$string['cannotmovefromto'] = 'Impossible de déplacer la catégorie $a[0] vers $a[1]';
$string['cannotmovequestion'] = 'Vous ne pouvez pas utiliser ce script pour déplacer des questions ayant des fichiers associés dans divers endroits.';
$string['cannotopenforwriting'] = 'Impossible d\'ouvrir en écriture $a';
$string['cannotpreview'] = 'Impossible de prévisualiser ces questions !';
$string['cannotretrieveqcat'] = 'Impossible de trouver la catégorie de questions';
$string['cannotsavequiz'] = 'Échec de la sauvegarde de la tentative actuelle à ce test !';
$string['cannotunhidequestion'] = 'Impossible de rendre visible cette question.';
$string['cannotunzip'] = 'Impossible de décompresser le fichier.';
$string['cannotupdatecate'] = 'Impossible de modifier la catégorie $a';
$string['cannotupdatecount'] = 'Erreur : impossible de modifier le numéro de l\'élément';
$string['cannotupdateitem'] = 'Erreur : impossible de modifier l\'élément du jeu de données';
$string['cannotupdatequestion'] = 'Impossible de modifier la question !';
$string['cannotupdatequestionver'] = 'Impossible de modifier la version de la question';
$string['cannotupdaterandomqname'] = 'Impossible de modifier le nom de la question aléatoire';
$string['cannotupdatesubcate'] = 'Une catégorie enfant n\'a pas pu être modifiée !';
$string['cannotwriteto'] = 'Impossible d\'écrire dans $a les questions exportées';
$string['categorycurrent'] = 'Catégorie';
$string['categorycurrentuse'] = 'Utiliser cette catégorie';
$string['categorydoesnotexist'] = 'Cette catégorie n\'existe pas';
$string['categorymoveto'] = 'Enregistrer dans la catégorie';
$string['changepublishstatuscat'] = 'La <a href=\"$a->caturl\">catégorie « {$a->name} »</a> du cours « {$a->coursename} » verra son état modifié de <strong>$a->changefrom à $a->changeto</strong>.';
$string['chooseqtypetoadd'] = 'Choisir un type de question à ajouter';
$string['clicktoflag'] = 'Cliquer pour marquer cette question';
$string['clicktounflag'] = 'Cliquer ne plus marquer cette question';
$string['contexterror'] = 'Vous ne devriez pas être arrivé ici si vous ne déplacez pas une catégorie vers un autre contexte.';
$string['copy'] = 'Copier depuis $a et modifier les liens.';
$string['created'] = 'Créée';
$string['createdby'] = 'Créée par';
$string['createdmodifiedheader'] = 'Créée / enregistrée';
$string['createnewquestion'] = 'Créer une question...';
$string['cwrqpfs'] = 'Questions aléatoires sélectionnant des questions dans les sous-catégories.';
$string['cwrqpfsinfo'] = '<p>Lors de la mise à jour à Moodle 1.9, les catégories de questions seront séparées en différents contextes. Certaines catégories et questions de votre site verront leur état de partage modifié. Cette opération est rarement nécessaire. Elle est effectuée lorsque l\'une ou plusieurs questions aléatoires d\'un test sont réglées de façon à sélectionner des questions dans des catégories partagées et non partagées (et c\'est le cas sur ce site). Cette situation survient quand une question aléatoire puise dans des sous-catégories et l\'une de ces sous-catégories a un statut de partage différent de la catégorie parente dans laquelle la question aléatoire a été créée.</p><p>Les catégories de question suivantes, d\'où sont puisées des questions aléatoires à partir de questions dans une catégorie parente, verront leur état de partage modifié de façon à correspondre à l\'état de partage de la catégorie parente lors de la mise à jour à Moodle 1.9. Les catégories ci-dessous sont concernées par ce changement. Les questions affectées par ce changement continueront à fonctionner dans tous les tests existants, jusqu\'à ce que vous les retiriez de ces tests.</p>';
$string['cwrqpfsnoprob'] = 'Aucune catégorie de question de votre site n\'est affectée par le problème des « Questions aléatoires sélectionnant des questions dans des sous-catégories.»';
$string['defaultfor'] = 'Défaut pour $a';
$string['defaultinfofor'] = 'La catégorie par défaut pour les questions partagées dans le contexte « {$a} ».';
$string['deletecoursecategorywithquestions'] = 'La banque de questions associée à cette catégorie contient des questions. Si vous continuez, ces questions seront supprimées. Si vous voulez les conserver, veuillez d\'abord les déplacer en utilisant l\'interface de la banque de questions.';
$string['disabled'] = 'Désactivé';
$string['disterror'] = 'La distribution $a a causé des problèmes';
$string['donothing'] = 'Ne pas copier ou déplacer les fichiers, ni modifier les liens.';
$string['editingcategory'] = 'Modifier une catégorie';
$string['editingquestion'] = 'Modifier une question';
$string['editthiscategory'] = 'Modifier cette catégorie';
$string['emptyxml'] = 'Erreur inconnue. Fichier imsmanifest.xml vide';
$string['enabled'] = 'Activé';
$string['erroraccessingcontext'] = 'Impossible d\'accéder au contexte';
$string['errordeletingquestionsfromcategory'] = 'Erreur lors de la suppression de questions de la catégorie $a.';
$string['errorduringpost'] = 'Erreur lors du post-traitement !';
$string['errorduringpre'] = 'Erreur lors du pré-traitement !';
$string['errorduringproc'] = 'Erreur lors du traitement !';
$string['errorduringregrade'] = 'Impossible de renoter la question $a->qid. Retour à l\'état $a->stateid.';
$string['errorfilecannotbecopied'] = 'Impossible de copier le fichier $a.';
$string['errorfilecannotbemoved'] = 'Impossible de déplacer le fichier $a.';
$string['errorfileschanged'] = 'Erreur : certains fichiers liés dans des questions ont été modifiés depuis l\'affichage du formulaire.';
$string['errormanualgradeoutofrange'] = 'La note $a->grade n\'est pas entre 0 et $a->maxgrade pour la question $a->name. Le score et le commentaire n\'ont pas été enregistrés.';
$string['errormovingquestions'] = 'Erreur lors du déplacement des questions d\'identifiants $a.';
$string['errorpostprocess'] = 'Erreur lors du post-traitement !';
$string['errorpreprocess'] = 'Erreur lors du pré-traitement !';
$string['errorprocess'] = 'Erreur lors du traitement !';
$string['errorprocessingresponses'] = 'Une erreur est survenue lors du traitement de vos réponses.';
$string['errorsavingcomment'] = 'Erreur lors de l\'enregistrement dans la base de données du commentaire pour la question $a->name.';
$string['errorupdatingattempt'] = 'Erreur lors de la mise à jour dans la base de données de la tentative $a->id.';
$string['exportcategory'] = 'Exporter catégorie';
$string['exporterror'] = 'Des erreurs sont survenues lors de l\'exportation !';
$string['filesareacourse'] = 'la zone des fichiers du cours';
$string['filesareasite'] = 'la zone des fichiers du site';
$string['filestomove'] = 'Déplacer / copier les fichiers vers {$a} ?';
$string['flagged'] = 'Marquée';
$string['flagthisquestion'] = 'Marquer cette question';
$string['formquestionnotinids'] = 'Le formulaire contient une question qui n\'apparaît pas dans les identifiants de question';
$string['fractionsnomax'] = 'L\'une des réponses doit donner un score de 100%% afin qu\'il soit possible d\'obtenir la totalité des points pour cette question.';
$string['getcategoryfromfile'] = 'Obtenir la catégorie à partir du fichier';
$string['getcontextfromfile'] = 'Obtenir le contexte à partir du fichier';
$string['ignorebroken'] = 'Ignorer les liens cassés';
$string['impossiblechar'] = 'Caractère impossible $a détecté comme séparateur';
$string['invalidarg'] = 'Aucun paramètre valide fourni ou configuration du serveur incorrecte';
$string['invalidcategoryidforparent'] = 'Identifiant de catégorie non valide pour le parent !';
$string['invalidcategoryidtomove'] = 'Identifiant de la catégorie à déplacer non valide !';
$string['invalidconfirm'] = 'La chaîne de confirmation est incorrecte';
$string['invalidcontextinhasanyquestions'] = 'Contexte non valide passé à la fonction question_context_has_any_questions().';
$string['invalidwizardpage'] = 'Page de l\'assistant incorrecte ou non spécifiée !';
$string['lastmodifiedby'] = 'Dernière modification par';
$string['linkedfiledoesntexist'] = 'Le fichier lié $a n\'existe pas';
$string['makechildof'] = 'Déplacer comme descendant de $a';
$string['maketoplevelitem'] = 'Déplacer au plus haut niveau';
$string['missingcourseorcmid'] = 'Vous devez fournir l\'identifiant de cours ou le numéro de cours pour imprimer la question.';
$string['missingcourseorcmidtolink'] = 'Vous devez fournir l\'identifiant de cours ou le numéro de cours à get_question_edit_link.';
$string['missingimportantcode'] = 'Il manque à ce type de question un code important : $a.';
$string['missingoption'] = 'Les options de la question cloze $a manquent';
$string['modified'] = 'Enregistré';
$string['move'] = 'Déplacer depuis $a et modifier les liens.';
$string['movecategory'] = 'Déplacer catégorie';
$string['movedquestionsandcategories'] = 'Les questions et catégories de questions ont été déplacées de $a->oldplace vers $a->newplace.';
$string['movelinksonly'] = 'Modifier les liens sans déplacer ni copier de fichier.';
$string['moveq'] = 'Déplacer question(s)';
$string['moveqtoanothercontext'] = 'Déplacer la question vers un autre contexte.';
$string['movingcategory'] = 'Déplacement catégorie';
$string['movingcategoryandfiles'] = 'Voulez-vous vraiment déplacer la catégorie {$a->name} et toutes les catégories filles vers le contexte « {$a->contextto} »?<br />{$a->urlcount} fichiers liés depuis des questions situées dans {$a->fromareaname} ont été détectés. Voulez-vous les copier ou les déplacer vers {$a->toareaname} ?';
$string['movingcategorynofiles'] = 'Voulez-vous vraiment déplacer la catégorie {$a->name} et toutes les catégories filles vers le contexte « {$a->contextto} »?';
$string['movingquestions'] = 'Déplacement des questions et des fichiers';
$string['movingquestionsandfiles'] = 'Voulez-vous vraiment déplacer la(les) question(s) {$a->questions} vers le contexte « {$a->tocontext} »?<br />{$a->urlcount} fichiers liés depuis cette(ces) question(s) dans {$a->fromareaname} ont été détectés.  Voulez-vous les copier ou les déplacer vers {$a->toareaname} ';
$string['movingquestionsnofiles'] =  'Voulez-vous vraiment déplacer la(les) question(s) {$a->questions} vers le contexte « {$a->tocontext} »?<br />Il n\'y a <strong>aucun fichier</strong> liés depuis cette(ces) question(s) dans {$a->fromareaname}.';
$string['needtochoosecat'] = 'Vous devez choisir une catégorie dans laquelle déplacer cette question ou cliquer sur « Annuler ».';
$string['nocate'] = 'Aucune catégorie {$a} !';
$string['nopermissionadd'] = 'Vous n\'avez pas le droit d\'ajouter des questions ici.';
$string['nopermissionmove'] = 'Vous n\'avez pas le droit de déplacer des questions depuis ici. Vous devez enregistrer la question dans cette catégorie ou l\'enregistrer comme nouvelle question.';
$string['noprobs'] = 'Aucun problème n\'a été détecté dans votre banque de questions.';
$string['notenoughdatatoeditaquestion'] = 'L\'identifiant de la question, l\'identifiant de la catégorie et le type de question n\'ont pas été spécifiés.';
$string['notenoughdatatomovequestions'] = 'Vous devez fournir les identifiants des questions que vous voulez déplacer.';
$string['notflagged'] = 'Non marquée';
$string['novirtualquestiontype'] = 'Il n\'y a pas de type de question virtuelle pour les questions de type $a';
$string['parenthesisinproperclose'] = 'La parenthèse avant ** n\'est pas fermée correctement dans $a**';
$string['parenthesisinproperstart'] = 'La parenthèse avant ** n\'est pas ouverte correctement dans $a**';
$string['permissionedit'] = 'Modifier cette question';
$string['permissionmove'] = 'Déplacer cette question';
$string['permissionsaveasnew'] = 'Enregistrer ceci en tant que nouvelle question';
$string['permissionto'] = 'Vous avez les autorisations pour :';
$string['published'] = 'partagée';
$string['qtypeveryshort'] = 'T';
$string['questionaffected'] = '<a href=\"$a->qurl\">La question « {$a->name} » ($a->qtype)</a> est dans cette catégorie, mais est aussi utilisée dans le <a href=\"$a->qurl\">test « {$a->quizname} »</a> dans le cours « {$a->coursename} ».';
$string['questionbank'] = 'Banque de questions';
$string['questioncategory'] = 'Catégorie de questions';
$string['questioncatsfor'] = 'Catégories de question de « {$a} »';
$string['questiondoesnotexist'] = 'Cette question n\'existe pas';
$string['questionname'] = 'Nom de question';
$string['questionsaveerror'] = 'Des erreurs sont survenues lors de l\'enregistrement de la question ($a)';
$string['questionsmovedto'] = 'Les questions encore utilisées ont été déplacées vers « {$a} » dans la catégorie de cours mère.';
$string['questionsrescuedfrom'] = 'Questions enregistrées depuis le contexte $a.';
$string['questionsrescuedfrominfo'] = 'Ces questions (dont certaines sont peut-être cachées) ont été enregistrées lors de la suppression du contexte $a, car elles sont encore utilisées dans certains tests ou d\'autres activités.';
$string['questiontype'] = 'Type de question';
$string['questionuse'] = 'Utilisation de question dans cette activité';
$string['saveflags'] = 'Enregistrer l\'état des marquages';
$string['selectacategory'] = 'Choisir une catégorie';
$string['selectaqtypefordescription'] = 'Choisir un type de question pour voir sa description.';
$string['selectquestionsforbulk'] = 'Sélectionner des questions pour des actions en masse';
$string['shareincontext'] = 'Partager dans le contexte pour $a';
$string['tofilecategory'] = 'Exporter la catégorie dans le fichier';
$string['tofilecontext'] = 'Exporter le contexte dans le fichier';
$string['unknown'] = 'Inconnu';
$string['unknownquestiontype'] = 'Type de question inconnu $a';
$string['unknowntolerance'] = 'Type de tolérance inconnu $a';
$string['unpublished'] = 'non partagée';
$string['upgradeproblemcategoryloop'] = 'Un problème a été détecté lors de la modification des catégories de question : il y a une boucle dans l\'arbre des catégories. Les identifiants des catégories touchés sont $a.';
$string['upgradeproblemcouldnotupdatecategory'] = 'Impossible de modifier la catégorie de question $a->name ($a->id).';
$string['upgradeproblemunknowncategory'] = 'Un problème a été détecté lors de la modification des catégories de question : la catégorie $a->id fait référence à la catégorie parente $a->parent, qui n\'existe pas. La catégorie parente a été changée pour corriger le problème.';
$string['wrongprefix'] = 'Le préfixe est mal formatté $a';
$string['yourfileshoulddownload'] = 'Le téléchargement de votre fichier d\'exportation va commencer. Si rien ne se passe, veuillez <a href=\"$a\">cliquer ici</a>.';

?>
