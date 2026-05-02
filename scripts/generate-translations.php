<?php
/**
 * Generate .po and .mo translation files for MSC Post Expiry.
 *
 * Usage: php scripts/generate-translations.php
 *
 * Reads the POT file, applies translations from built-in dictionaries,
 * writes .po files, and compiles .mo files via msgfmt.
 *
 * @package MSCPE
 */

// phpcs:disable WordPress

$plugin_dir = dirname( __DIR__ );
$pot_file   = $plugin_dir . '/languages/msc-post-expiry.pot';
$lang_dir   = $plugin_dir . '/languages';

if ( ! file_exists( $pot_file ) ) {
	echo "ERROR: POT file not found at {$pot_file}\n";
	exit( 1 );
}

// ----- Parse POT file -----
$pot_content = file_get_contents( $pot_file );
$entries     = array();
$lines       = explode( "\n", $pot_content );
$i           = 0;
$count       = count( $lines );

while ( $i < $count ) {
	$line = $lines[ $i ];

	// Look for msgid lines (skip the header empty msgid).
	if ( preg_match( '/^msgid "(.+)"$/', $line, $m ) ) {
		$msgid = $m[1];

		// Handle multi-line msgid.
		while ( isset( $lines[ $i + 1 ] ) && preg_match( '/^"(.+)"$/', $lines[ $i + 1 ], $cont ) ) {
			$msgid .= $cont[1];
			++$i;
		}

		// Check for msgid_plural.
		$msgid_plural = '';
		if ( isset( $lines[ $i + 1 ] ) && preg_match( '/^msgid_plural "(.+)"$/', $lines[ $i + 1 ], $pm ) ) {
			$msgid_plural = $pm[1];
			++$i;
			// Handle multi-line msgid_plural.
			while ( isset( $lines[ $i + 1 ] ) && preg_match( '/^"(.+)"$/', $lines[ $i + 1 ], $cont ) ) {
				$msgid_plural .= $cont[1];
				++$i;
			}
		}

		$entries[] = array(
			'msgid'        => $msgid,
			'msgid_plural' => $msgid_plural,
		);
	}
	++$i;
}

echo "Parsed " . count( $entries ) . " POT entries.\n";

// ----- Translation Dictionaries -----

// German (Germany)
$de_DE = array(
	'MSC Post Expiry' => 'MSC Post Expiry',
	'https://anomalous.co.za' => 'https://anomalous.co.za',
	'Automatically expire posts and pages on a scheduled date. Set expiration dates in the post editor and let the plugin handle the rest.' => 'Beiträge und Seiten automatisch an einem geplanten Datum ablaufen lassen. Legen Sie Ablaufdaten im Beitragseditor fest und lassen Sie das Plugin den Rest erledigen.',
	'Anomalous Developers' => 'Anomalous Developers',
	'You do not have permission to access this page.' => 'Sie haben keine Berechtigung, auf diese Seite zuzugreifen.',
	'Security check failed. Please try again.' => 'Die Sicherheitsüberprüfung ist fehlgeschlagen. Bitte versuchen Sie es erneut.',
	'Settings' => 'Einstellungen',
	'Support' => 'Support',
	'Settings saved.' => 'Einstellungen gespeichert.',
	'Move to Trash' => 'In den Papierkorb verschieben',
	'Permanently Delete' => 'Endgültig löschen',
	'Change to Draft' => 'Zu Entwurf ändern',
	'Change to Private' => 'Zu Privat ändern',
	'Move to Category' => 'In Kategorie verschieben',
	'Enable post expiry' => 'Beitragsablauf aktivieren',
	'Allow posts to expire on a scheduled date.' => 'Beiträge an einem geplanten Datum ablaufen lassen.',
	'Post type mode' => 'Beitragstyp-Modus',
	'Enable expiry only on selected post types' => 'Ablauf nur für ausgewählte Beitragstypen aktivieren',
	'Enable expiry on all public post types except selected' => 'Ablauf für alle öffentlichen Beitragstypen außer den ausgewählten aktivieren',
	'Post types' => 'Beitragstypen',
	'Expiry action' => 'Ablaufaktion',
	'What should happen when a post expires.' => 'Was soll geschehen, wenn ein Beitrag abläuft.',
	'Expiry category' => 'Ablaufkategorie',
	'Select a category' => 'Kategorie auswählen',
	'Posts will be moved to this category when expired. Only used when "Move to Category" is selected above.' => 'Beiträge werden bei Ablauf in diese Kategorie verschoben. Wird nur verwendet, wenn oben „In Kategorie verschieben" ausgewählt ist.',
	'Save Settings' => 'Einstellungen speichern',
	'How to Use Post Expiry' => 'So verwenden Sie den Beitragsablauf',
	'Post Expiry allows you to automatically handle posts when they reach a specified expiration date.' => 'Mit dem Beitragsablauf können Sie Beiträge automatisch verwalten, wenn sie ein festgelegtes Ablaufdatum erreichen.',
	'Setting an Expiry Date' => 'Ein Ablaufdatum festlegen',
	'When editing a post or page, look for the "Post Expiry" box in the sidebar on the right. Enter the date and time when you want the post to expire.' => 'Suchen Sie beim Bearbeiten eines Beitrags oder einer Seite nach der Box „Beitragsablauf" in der Seitenleiste rechts. Geben Sie das Datum und die Uhrzeit ein, zu der der Beitrag ablaufen soll.',
	'Expiry Actions' => 'Ablaufaktionen',
	'When a post expires, one of the following actions will occur based on your settings:' => 'Wenn ein Beitrag abläuft, wird basierend auf Ihren Einstellungen eine der folgenden Aktionen ausgeführt:',
	'The post is moved to trash and no longer visible to visitors.' => 'Der Beitrag wird in den Papierkorb verschoben und ist für Besucher nicht mehr sichtbar.',
	'The post is permanently deleted from your site.' => 'Der Beitrag wird dauerhaft von Ihrer Website gelöscht.',
	'The post is changed to draft status and hidden from visitors.' => 'Der Beitrag wird in den Entwurfsstatus geändert und vor Besuchern verborgen.',
	'The post is changed to private status and only visible to logged-in users with appropriate permissions.' => 'Der Beitrag wird auf den privaten Status geändert und ist nur für angemeldete Benutzer mit entsprechenden Berechtigungen sichtbar.',
	'The post is moved to a specific archive category. Configure the category in the Settings tab.' => 'Der Beitrag wird in eine bestimmte Archivkategorie verschoben. Konfigurieren Sie die Kategorie im Einstellungen-Tab.',
	'Post Type Configuration' => 'Beitragstyp-Konfiguration',
	'Use the Settings tab to choose which post types support expiry dates. You can either enable expiry on specific post types or disable it on specific types while enabling it on all others.' => 'Verwenden Sie den Einstellungen-Tab, um auszuwählen, welche Beitragstypen Ablaufdaten unterstützen. Sie können den Ablauf für bestimmte Beitragstypen aktivieren oder für bestimmte Typen deaktivieren und für alle anderen aktivieren.',
	'Frequently Asked Questions' => 'Häufig gestellte Fragen',
	'The Post Expiry metabox is not showing on my posts.' => 'Die Beitragsablauf-Metabox wird bei meinen Beiträgen nicht angezeigt.',
	'Check that "Enable post expiry" is ticked on the Settings tab.' => 'Überprüfen Sie, ob „Beitragsablauf aktivieren" im Einstellungen-Tab aktiviert ist.',
	'Check that the post type (e.g. Post, Page) is selected in the Post types list.' => 'Überprüfen Sie, ob der Beitragstyp (z. B. Beitrag, Seite) in der Beitragstypenliste ausgewählt ist.',
	'The metabox appears in the sidebar on the right when editing a post.' => 'Die Metabox erscheint in der Seitenleiste rechts beim Bearbeiten eines Beitrags.',
	'When does the expiry action occur?' => 'Wann wird die Ablaufaktion ausgeführt?',
	'Post expiry is processed by WordPress scheduled events (cron). The action will occur shortly after the expiry date and time passes. The exact timing depends on your site traffic and WordPress cron configuration.' => 'Der Beitragsablauf wird durch geplante WordPress-Ereignisse (Cron) verarbeitet. Die Aktion wird kurz nach Ablauf von Datum und Uhrzeit ausgeführt. Der genaue Zeitpunkt hängt von Ihrem Website-Traffic und der WordPress-Cron-Konfiguration ab.',
	'Can I disable expiry for a specific post?' => 'Kann ich den Ablauf für einen bestimmten Beitrag deaktivieren?',
	'Yes. Simply leave the expiry date and time fields empty in the Post Expiry metabox.' => 'Ja. Lassen Sie einfach die Felder für Ablaufdatum und -zeit in der Beitragsablauf-Metabox leer.',
	'Need Help?' => 'Hilfe benötigt?',
	'If you have questions, encounter bugs, or need setup assistance, we\'re here to help.' => 'Wenn Sie Fragen haben, Fehler finden oder Hilfe bei der Einrichtung benötigen, sind wir für Sie da.',
	'Get Support' => 'Support erhalten',
	'Post Expiry' => 'Beitragsablauf',
	'Expiry Date' => 'Ablaufdatum',
	'Expiry Time' => 'Ablaufzeit',
	'Leave empty to disable expiry for this post.' => 'Leer lassen, um den Ablauf für diesen Beitrag zu deaktivieren.',
	'Every 5 minutes' => 'Alle 5 Minuten',
	'No expiry set' => 'Kein Ablauf festgelegt',
	'Expired' => 'Abgelaufen',
	'Expires soon' => 'Läuft bald ab',
);

$de_DE_plural = array(
	'%d day remaining' => array( '%d Tag verbleibend', '%d Tage verbleibend' ),
	'%d hour remaining' => array( '%d Stunde verbleibend', '%d Stunden verbleibend' ),
);

// Spanish (Spain)
$es_ES = array(
	'MSC Post Expiry' => 'MSC Post Expiry',
	'https://anomalous.co.za' => 'https://anomalous.co.za',
	'Automatically expire posts and pages on a scheduled date. Set expiration dates in the post editor and let the plugin handle the rest.' => 'Expirar automáticamente entradas y páginas en una fecha programada. Establezca fechas de vencimiento en el editor de entradas y deje que el plugin se encargue del resto.',
	'Anomalous Developers' => 'Anomalous Developers',
	'You do not have permission to access this page.' => 'No tiene permiso para acceder a esta página.',
	'Security check failed. Please try again.' => 'La verificación de seguridad falló. Por favor, inténtelo de nuevo.',
	'Settings' => 'Ajustes',
	'Support' => 'Soporte',
	'Settings saved.' => 'Ajustes guardados.',
	'Move to Trash' => 'Mover a la papelera',
	'Permanently Delete' => 'Eliminar permanentemente',
	'Change to Draft' => 'Cambiar a borrador',
	'Change to Private' => 'Cambiar a privado',
	'Move to Category' => 'Mover a categoría',
	'Enable post expiry' => 'Habilitar vencimiento de entradas',
	'Allow posts to expire on a scheduled date.' => 'Permitir que las entradas venzan en una fecha programada.',
	'Post type mode' => 'Modo de tipo de entrada',
	'Enable expiry only on selected post types' => 'Habilitar vencimiento solo en los tipos de entrada seleccionados',
	'Enable expiry on all public post types except selected' => 'Habilitar vencimiento en todos los tipos de entrada públicos excepto los seleccionados',
	'Post types' => 'Tipos de entrada',
	'Expiry action' => 'Acción de vencimiento',
	'What should happen when a post expires.' => 'Qué debe suceder cuando una entrada vence.',
	'Expiry category' => 'Categoría de vencimiento',
	'Select a category' => 'Seleccionar una categoría',
	'Posts will be moved to this category when expired. Only used when "Move to Category" is selected above.' => 'Las entradas se moverán a esta categoría cuando venzan. Solo se usa cuando se selecciona «Mover a categoría» arriba.',
	'Save Settings' => 'Guardar ajustes',
	'How to Use Post Expiry' => 'Cómo usar el vencimiento de entradas',
	'Post Expiry allows you to automatically handle posts when they reach a specified expiration date.' => 'El vencimiento de entradas le permite gestionar automáticamente las entradas cuando alcanzan una fecha de vencimiento especificada.',
	'Setting an Expiry Date' => 'Establecer una fecha de vencimiento',
	'When editing a post or page, look for the "Post Expiry" box in the sidebar on the right. Enter the date and time when you want the post to expire.' => 'Al editar una entrada o página, busque el cuadro «Vencimiento de entrada» en la barra lateral derecha. Introduzca la fecha y hora en que desea que la entrada venza.',
	'Expiry Actions' => 'Acciones de vencimiento',
	'When a post expires, one of the following actions will occur based on your settings:' => 'Cuando una entrada vence, se realizará una de las siguientes acciones según sus ajustes:',
	'The post is moved to trash and no longer visible to visitors.' => 'La entrada se mueve a la papelera y ya no es visible para los visitantes.',
	'The post is permanently deleted from your site.' => 'La entrada se elimina permanentemente de su sitio.',
	'The post is changed to draft status and hidden from visitors.' => 'La entrada se cambia a estado de borrador y se oculta de los visitantes.',
	'The post is changed to private status and only visible to logged-in users with appropriate permissions.' => 'La entrada se cambia a estado privado y solo es visible para usuarios conectados con los permisos apropiados.',
	'The post is moved to a specific archive category. Configure the category in the Settings tab.' => 'La entrada se mueve a una categoría de archivo específica. Configure la categoría en la pestaña de ajustes.',
	'Post Type Configuration' => 'Configuración de tipos de entrada',
	'Use the Settings tab to choose which post types support expiry dates. You can either enable expiry on specific post types or disable it on specific types while enabling it on all others.' => 'Use la pestaña de ajustes para elegir qué tipos de entrada admiten fechas de vencimiento. Puede habilitar el vencimiento en tipos específicos o deshabilitarlo en tipos específicos mientras lo habilita en todos los demás.',
	'Frequently Asked Questions' => 'Preguntas frecuentes',
	'The Post Expiry metabox is not showing on my posts.' => 'La metabox de vencimiento de entrada no se muestra en mis entradas.',
	'Check that "Enable post expiry" is ticked on the Settings tab.' => 'Verifique que «Habilitar vencimiento de entradas» esté activado en la pestaña de ajustes.',
	'Check that the post type (e.g. Post, Page) is selected in the Post types list.' => 'Verifique que el tipo de entrada (por ejemplo, Entrada, Página) esté seleccionado en la lista de tipos de entrada.',
	'The metabox appears in the sidebar on the right when editing a post.' => 'La metabox aparece en la barra lateral derecha al editar una entrada.',
	'When does the expiry action occur?' => '¿Cuándo se ejecuta la acción de vencimiento?',
	'Post expiry is processed by WordPress scheduled events (cron). The action will occur shortly after the expiry date and time passes. The exact timing depends on your site traffic and WordPress cron configuration.' => 'El vencimiento de entradas se procesa mediante eventos programados de WordPress (cron). La acción se ejecutará poco después de que pasen la fecha y hora de vencimiento. El momento exacto depende del tráfico de su sitio y la configuración del cron de WordPress.',
	'Can I disable expiry for a specific post?' => '¿Puedo desactivar el vencimiento para una entrada específica?',
	'Yes. Simply leave the expiry date and time fields empty in the Post Expiry metabox.' => 'Sí. Simplemente deje vacíos los campos de fecha y hora de vencimiento en la metabox de vencimiento de entrada.',
	'Need Help?' => '¿Necesita ayuda?',
	'If you have questions, encounter bugs, or need setup assistance, we\'re here to help.' => 'Si tiene preguntas, encuentra errores o necesita ayuda con la configuración, estamos aquí para ayudarle.',
	'Get Support' => 'Obtener soporte',
	'Post Expiry' => 'Vencimiento de entrada',
	'Expiry Date' => 'Fecha de vencimiento',
	'Expiry Time' => 'Hora de vencimiento',
	'Leave empty to disable expiry for this post.' => 'Dejar vacío para desactivar el vencimiento de esta entrada.',
	'Every 5 minutes' => 'Cada 5 minutos',
	'No expiry set' => 'Sin vencimiento establecido',
	'Expired' => 'Vencido',
	'Expires soon' => 'Vence pronto',
);

$es_ES_plural = array(
	'%d day remaining' => array( '%d día restante', '%d días restantes' ),
	'%d hour remaining' => array( '%d hora restante', '%d horas restantes' ),
);

// French (France)
$fr_FR = array(
	'MSC Post Expiry' => 'MSC Post Expiry',
	'https://anomalous.co.za' => 'https://anomalous.co.za',
	'Automatically expire posts and pages on a scheduled date. Set expiration dates in the post editor and let the plugin handle the rest.' => 'Faire expirer automatiquement les articles et les pages à une date programmée. Définissez les dates d\'expiration dans l\'éditeur d\'articles et laissez l\'extension faire le reste.',
	'Anomalous Developers' => 'Anomalous Developers',
	'You do not have permission to access this page.' => 'Vous n\'avez pas la permission d\'accéder à cette page.',
	'Security check failed. Please try again.' => 'La vérification de sécurité a échoué. Veuillez réessayer.',
	'Settings' => 'Réglages',
	'Support' => 'Support',
	'Settings saved.' => 'Réglages enregistrés.',
	'Move to Trash' => 'Mettre à la corbeille',
	'Permanently Delete' => 'Supprimer définitivement',
	'Change to Draft' => 'Passer en brouillon',
	'Change to Private' => 'Passer en privé',
	'Move to Category' => 'Déplacer vers une catégorie',
	'Enable post expiry' => 'Activer l\'expiration des articles',
	'Allow posts to expire on a scheduled date.' => 'Permettre aux articles d\'expirer à une date programmée.',
	'Post type mode' => 'Mode de type de contenu',
	'Enable expiry only on selected post types' => 'Activer l\'expiration uniquement sur les types de contenu sélectionnés',
	'Enable expiry on all public post types except selected' => 'Activer l\'expiration sur tous les types de contenu publics sauf les sélectionnés',
	'Post types' => 'Types de contenu',
	'Expiry action' => 'Action d\'expiration',
	'What should happen when a post expires.' => 'Ce qui doit se passer lorsqu\'un article expire.',
	'Expiry category' => 'Catégorie d\'expiration',
	'Select a category' => 'Sélectionner une catégorie',
	'Posts will be moved to this category when expired. Only used when "Move to Category" is selected above.' => 'Les articles seront déplacés vers cette catégorie à leur expiration. Utilisé uniquement lorsque « Déplacer vers une catégorie » est sélectionné ci-dessus.',
	'Save Settings' => 'Enregistrer les réglages',
	'How to Use Post Expiry' => 'Comment utiliser l\'expiration des articles',
	'Post Expiry allows you to automatically handle posts when they reach a specified expiration date.' => 'L\'expiration des articles vous permet de gérer automatiquement les articles lorsqu\'ils atteignent une date d\'expiration spécifiée.',
	'Setting an Expiry Date' => 'Définir une date d\'expiration',
	'When editing a post or page, look for the "Post Expiry" box in the sidebar on the right. Enter the date and time when you want the post to expire.' => 'Lors de l\'édition d\'un article ou d\'une page, cherchez la boîte « Expiration de l\'article » dans la barre latérale à droite. Entrez la date et l\'heure auxquelles vous souhaitez que l\'article expire.',
	'Expiry Actions' => 'Actions d\'expiration',
	'When a post expires, one of the following actions will occur based on your settings:' => 'Lorsqu\'un article expire, l\'une des actions suivantes sera effectuée selon vos réglages :',
	'The post is moved to trash and no longer visible to visitors.' => 'L\'article est mis à la corbeille et n\'est plus visible pour les visiteurs.',
	'The post is permanently deleted from your site.' => 'L\'article est définitivement supprimé de votre site.',
	'The post is changed to draft status and hidden from visitors.' => 'L\'article passe en statut brouillon et est masqué aux visiteurs.',
	'The post is changed to private status and only visible to logged-in users with appropriate permissions.' => 'L\'article passe en statut privé et n\'est visible que par les utilisateurs connectés ayant les permissions appropriées.',
	'The post is moved to a specific archive category. Configure the category in the Settings tab.' => 'L\'article est déplacé vers une catégorie d\'archive spécifique. Configurez la catégorie dans l\'onglet Réglages.',
	'Post Type Configuration' => 'Configuration des types de contenu',
	'Use the Settings tab to choose which post types support expiry dates. You can either enable expiry on specific post types or disable it on specific types while enabling it on all others.' => 'Utilisez l\'onglet Réglages pour choisir quels types de contenu prennent en charge les dates d\'expiration. Vous pouvez activer l\'expiration sur des types spécifiques ou la désactiver sur certains types tout en l\'activant sur tous les autres.',
	'Frequently Asked Questions' => 'Foire aux questions',
	'The Post Expiry metabox is not showing on my posts.' => 'La boîte méta d\'expiration ne s\'affiche pas sur mes articles.',
	'Check that "Enable post expiry" is ticked on the Settings tab.' => 'Vérifiez que « Activer l\'expiration des articles » est coché dans l\'onglet Réglages.',
	'Check that the post type (e.g. Post, Page) is selected in the Post types list.' => 'Vérifiez que le type de contenu (par ex. Article, Page) est sélectionné dans la liste des types de contenu.',
	'The metabox appears in the sidebar on the right when editing a post.' => 'La boîte méta apparaît dans la barre latérale à droite lors de l\'édition d\'un article.',
	'When does the expiry action occur?' => 'Quand l\'action d\'expiration se produit-elle ?',
	'Post expiry is processed by WordPress scheduled events (cron). The action will occur shortly after the expiry date and time passes. The exact timing depends on your site traffic and WordPress cron configuration.' => 'L\'expiration des articles est traitée par les événements programmés de WordPress (cron). L\'action sera effectuée peu après la date et l\'heure d\'expiration. Le moment exact dépend du trafic de votre site et de la configuration du cron WordPress.',
	'Can I disable expiry for a specific post?' => 'Puis-je désactiver l\'expiration pour un article spécifique ?',
	'Yes. Simply leave the expiry date and time fields empty in the Post Expiry metabox.' => 'Oui. Laissez simplement les champs de date et d\'heure d\'expiration vides dans la boîte méta d\'expiration.',
	'Need Help?' => 'Besoin d\'aide ?',
	'If you have questions, encounter bugs, or need setup assistance, we\'re here to help.' => 'Si vous avez des questions, rencontrez des bugs ou avez besoin d\'aide pour la configuration, nous sommes là pour vous aider.',
	'Get Support' => 'Obtenir de l\'aide',
	'Post Expiry' => 'Expiration de l\'article',
	'Expiry Date' => 'Date d\'expiration',
	'Expiry Time' => 'Heure d\'expiration',
	'Leave empty to disable expiry for this post.' => 'Laisser vide pour désactiver l\'expiration de cet article.',
	'Every 5 minutes' => 'Toutes les 5 minutes',
	'No expiry set' => 'Aucune expiration définie',
	'Expired' => 'Expiré',
	'Expires soon' => 'Expire bientôt',
);

$fr_FR_plural = array(
	'%d day remaining' => array( '%d jour restant', '%d jours restants' ),
	'%d hour remaining' => array( '%d heure restante', '%d heures restantes' ),
);

// Italian (Italy)
$it_IT = array(
	'MSC Post Expiry' => 'MSC Post Expiry',
	'https://anomalous.co.za' => 'https://anomalous.co.za',
	'Automatically expire posts and pages on a scheduled date. Set expiration dates in the post editor and let the plugin handle the rest.' => 'Fai scadere automaticamente articoli e pagine a una data programmata. Imposta le date di scadenza nell\'editor degli articoli e lascia che il plugin faccia il resto.',
	'Anomalous Developers' => 'Anomalous Developers',
	'You do not have permission to access this page.' => 'Non hai il permesso di accedere a questa pagina.',
	'Security check failed. Please try again.' => 'Controllo di sicurezza fallito. Per favore riprova.',
	'Settings' => 'Impostazioni',
	'Support' => 'Supporto',
	'Settings saved.' => 'Impostazioni salvate.',
	'Move to Trash' => 'Sposta nel cestino',
	'Permanently Delete' => 'Elimina definitivamente',
	'Change to Draft' => 'Cambia in bozza',
	'Change to Private' => 'Cambia in privato',
	'Move to Category' => 'Sposta nella categoria',
	'Enable post expiry' => 'Abilita scadenza articoli',
	'Allow posts to expire on a scheduled date.' => 'Consenti agli articoli di scadere a una data programmata.',
	'Post type mode' => 'Modalità tipo di contenuto',
	'Enable expiry only on selected post types' => 'Abilita la scadenza solo sui tipi di contenuto selezionati',
	'Enable expiry on all public post types except selected' => 'Abilita la scadenza su tutti i tipi di contenuto pubblici tranne quelli selezionati',
	'Post types' => 'Tipi di contenuto',
	'Expiry action' => 'Azione di scadenza',
	'What should happen when a post expires.' => 'Cosa dovrebbe succedere quando un articolo scade.',
	'Expiry category' => 'Categoria di scadenza',
	'Select a category' => 'Seleziona una categoria',
	'Posts will be moved to this category when expired. Only used when "Move to Category" is selected above.' => 'Gli articoli verranno spostati in questa categoria alla scadenza. Utilizzato solo quando sopra è selezionato «Sposta nella categoria».',
	'Save Settings' => 'Salva impostazioni',
	'How to Use Post Expiry' => 'Come usare la scadenza degli articoli',
	'Post Expiry allows you to automatically handle posts when they reach a specified expiration date.' => 'La scadenza degli articoli consente di gestire automaticamente gli articoli quando raggiungono una data di scadenza specificata.',
	'Setting an Expiry Date' => 'Impostare una data di scadenza',
	'When editing a post or page, look for the "Post Expiry" box in the sidebar on the right. Enter the date and time when you want the post to expire.' => 'Durante la modifica di un articolo o una pagina, cerca la casella «Scadenza articolo» nella barra laterale a destra. Inserisci la data e l\'ora in cui desideri che l\'articolo scada.',
	'Expiry Actions' => 'Azioni di scadenza',
	'When a post expires, one of the following actions will occur based on your settings:' => 'Quando un articolo scade, verrà eseguita una delle seguenti azioni in base alle tue impostazioni:',
	'The post is moved to trash and no longer visible to visitors.' => 'L\'articolo viene spostato nel cestino e non è più visibile ai visitatori.',
	'The post is permanently deleted from your site.' => 'L\'articolo viene eliminato definitivamente dal tuo sito.',
	'The post is changed to draft status and hidden from visitors.' => 'L\'articolo viene cambiato in stato di bozza e nascosto ai visitatori.',
	'The post is changed to private status and only visible to logged-in users with appropriate permissions.' => 'L\'articolo viene cambiato in stato privato ed è visibile solo agli utenti connessi con i permessi appropriati.',
	'The post is moved to a specific archive category. Configure the category in the Settings tab.' => 'L\'articolo viene spostato in una categoria di archivio specifica. Configura la categoria nella scheda Impostazioni.',
	'Post Type Configuration' => 'Configurazione dei tipi di contenuto',
	'Use the Settings tab to choose which post types support expiry dates. You can either enable expiry on specific post types or disable it on specific types while enabling it on all others.' => 'Usa la scheda Impostazioni per scegliere quali tipi di contenuto supportano le date di scadenza. Puoi abilitare la scadenza su tipi specifici o disabilitarla su tipi specifici abilitandola su tutti gli altri.',
	'Frequently Asked Questions' => 'Domande frequenti',
	'The Post Expiry metabox is not showing on my posts.' => 'La metabox di scadenza non viene mostrata nei miei articoli.',
	'Check that "Enable post expiry" is ticked on the Settings tab.' => 'Verifica che «Abilita scadenza articoli» sia selezionato nella scheda Impostazioni.',
	'Check that the post type (e.g. Post, Page) is selected in the Post types list.' => 'Verifica che il tipo di contenuto (es. Articolo, Pagina) sia selezionato nell\'elenco dei tipi di contenuto.',
	'The metabox appears in the sidebar on the right when editing a post.' => 'La metabox appare nella barra laterale a destra durante la modifica di un articolo.',
	'When does the expiry action occur?' => 'Quando viene eseguita l\'azione di scadenza?',
	'Post expiry is processed by WordPress scheduled events (cron). The action will occur shortly after the expiry date and time passes. The exact timing depends on your site traffic and WordPress cron configuration.' => 'La scadenza degli articoli viene elaborata dagli eventi programmati di WordPress (cron). L\'azione verrà eseguita poco dopo la data e l\'ora di scadenza. Il momento esatto dipende dal traffico del tuo sito e dalla configurazione cron di WordPress.',
	'Can I disable expiry for a specific post?' => 'Posso disabilitare la scadenza per un articolo specifico?',
	'Yes. Simply leave the expiry date and time fields empty in the Post Expiry metabox.' => 'Sì. Lascia semplicemente vuoti i campi data e ora di scadenza nella metabox di scadenza.',
	'Need Help?' => 'Hai bisogno di aiuto?',
	'If you have questions, encounter bugs, or need setup assistance, we\'re here to help.' => 'Se hai domande, trovi bug o hai bisogno di assistenza per la configurazione, siamo qui per aiutarti.',
	'Get Support' => 'Ottieni supporto',
	'Post Expiry' => 'Scadenza articolo',
	'Expiry Date' => 'Data di scadenza',
	'Expiry Time' => 'Ora di scadenza',
	'Leave empty to disable expiry for this post.' => 'Lascia vuoto per disabilitare la scadenza per questo articolo.',
	'Every 5 minutes' => 'Ogni 5 minuti',
	'No expiry set' => 'Nessuna scadenza impostata',
	'Expired' => 'Scaduto',
	'Expires soon' => 'Scade presto',
);

$it_IT_plural = array(
	'%d day remaining' => array( '%d giorno rimanente', '%d giorni rimanenti' ),
	'%d hour remaining' => array( '%d ora rimanente', '%d ore rimanenti' ),
);

// Japanese
$ja = array(
	'MSC Post Expiry' => 'MSC Post Expiry',
	'https://anomalous.co.za' => 'https://anomalous.co.za',
	'Automatically expire posts and pages on a scheduled date. Set expiration dates in the post editor and let the plugin handle the rest.' => '投稿とページを予定日に自動的に期限切れにします。投稿エディターで有効期限を設定すれば、あとはプラグインにお任せください。',
	'Anomalous Developers' => 'Anomalous Developers',
	'You do not have permission to access this page.' => 'このページにアクセスする権限がありません。',
	'Security check failed. Please try again.' => 'セキュリティチェックに失敗しました。もう一度お試しください。',
	'Settings' => '設定',
	'Support' => 'サポート',
	'Settings saved.' => '設定を保存しました。',
	'Move to Trash' => 'ゴミ箱に移動',
	'Permanently Delete' => '完全に削除',
	'Change to Draft' => '下書きに変更',
	'Change to Private' => '非公開に変更',
	'Move to Category' => 'カテゴリーに移動',
	'Enable post expiry' => '投稿の期限切れを有効にする',
	'Allow posts to expire on a scheduled date.' => '投稿を予定日に期限切れにできるようにする。',
	'Post type mode' => '投稿タイプモード',
	'Enable expiry only on selected post types' => '選択した投稿タイプのみで期限切れを有効にする',
	'Enable expiry on all public post types except selected' => '選択した投稿タイプを除くすべての公開投稿タイプで期限切れを有効にする',
	'Post types' => '投稿タイプ',
	'Expiry action' => '期限切れアクション',
	'What should happen when a post expires.' => '投稿が期限切れになったときに何が起こるか。',
	'Expiry category' => '期限切れカテゴリー',
	'Select a category' => 'カテゴリーを選択',
	'Posts will be moved to this category when expired. Only used when "Move to Category" is selected above.' => '期限切れになると投稿はこのカテゴリーに移動されます。上で「カテゴリーに移動」が選択されている場合のみ使用されます。',
	'Save Settings' => '設定を保存',
	'How to Use Post Expiry' => '投稿期限切れの使い方',
	'Post Expiry allows you to automatically handle posts when they reach a specified expiration date.' => '投稿期限切れにより、投稿が指定された有効期限に達したときに自動的に処理できます。',
	'Setting an Expiry Date' => '有効期限の設定',
	'When editing a post or page, look for the "Post Expiry" box in the sidebar on the right. Enter the date and time when you want the post to expire.' => '投稿やページを編集する際、右側のサイドバーにある「投稿期限切れ」ボックスを探してください。投稿を期限切れにしたい日時を入力してください。',
	'Expiry Actions' => '期限切れアクション',
	'When a post expires, one of the following actions will occur based on your settings:' => '投稿が期限切れになると、設定に基づいて以下のアクションのいずれかが実行されます：',
	'The post is moved to trash and no longer visible to visitors.' => '投稿はゴミ箱に移動され、訪問者には表示されなくなります。',
	'The post is permanently deleted from your site.' => '投稿はサイトから完全に削除されます。',
	'The post is changed to draft status and hidden from visitors.' => '投稿は下書きステータスに変更され、訪問者から非表示になります。',
	'The post is changed to private status and only visible to logged-in users with appropriate permissions.' => '投稿は非公開ステータスに変更され、適切な権限を持つログインユーザーのみに表示されます。',
	'The post is moved to a specific archive category. Configure the category in the Settings tab.' => '投稿は特定のアーカイブカテゴリーに移動されます。設定タブでカテゴリーを設定してください。',
	'Post Type Configuration' => '投稿タイプの設定',
	'Use the Settings tab to choose which post types support expiry dates. You can either enable expiry on specific post types or disable it on specific types while enabling it on all others.' => '設定タブを使用して、どの投稿タイプが有効期限をサポートするかを選択してください。特定の投稿タイプで期限切れを有効にするか、特定のタイプで無効にしながら他のすべてで有効にすることができます。',
	'Frequently Asked Questions' => 'よくある質問',
	'The Post Expiry metabox is not showing on my posts.' => '投稿期限切れのメタボックスが投稿に表示されません。',
	'Check that "Enable post expiry" is ticked on the Settings tab.' => '設定タブで「投稿の期限切れを有効にする」がチェックされていることを確認してください。',
	'Check that the post type (e.g. Post, Page) is selected in the Post types list.' => '投稿タイプ（例：投稿、固定ページ）が投稿タイプリストで選択されていることを確認してください。',
	'The metabox appears in the sidebar on the right when editing a post.' => 'メタボックスは投稿を編集する際、右側のサイドバーに表示されます。',
	'When does the expiry action occur?' => '期限切れアクションはいつ実行されますか？',
	'Post expiry is processed by WordPress scheduled events (cron). The action will occur shortly after the expiry date and time passes. The exact timing depends on your site traffic and WordPress cron configuration.' => '投稿の期限切れはWordPressのスケジュールイベント（cron）によって処理されます。アクションは有効期限の日時が過ぎた直後に実行されます。正確なタイミングはサイトのトラフィックとWordPressのcron設定に依存します。',
	'Can I disable expiry for a specific post?' => '特定の投稿の期限切れを無効にできますか？',
	'Yes. Simply leave the expiry date and time fields empty in the Post Expiry metabox.' => 'はい。投稿期限切れメタボックスの日付と時間のフィールドを空のままにしてください。',
	'Need Help?' => 'お困りですか？',
	'If you have questions, encounter bugs, or need setup assistance, we\'re here to help.' => 'ご質問がある場合、バグを見つけた場合、またはセットアップの支援が必要な場合は、お気軽にお問い合わせください。',
	'Get Support' => 'サポートを受ける',
	'Post Expiry' => '投稿期限切れ',
	'Expiry Date' => '有効期限日',
	'Expiry Time' => '有効期限時刻',
	'Leave empty to disable expiry for this post.' => 'この投稿の期限切れを無効にするには空のままにしてください。',
	'Every 5 minutes' => '5分ごと',
	'No expiry set' => '期限切れ未設定',
	'Expired' => '期限切れ',
	'Expires soon' => 'まもなく期限切れ',
);

$ja_plural = array(
	'%d day remaining' => array( '残り%d日', '残り%d日' ),
	'%d hour remaining' => array( '残り%d時間', '残り%d時間' ),
);

// Dutch (Netherlands)
$nl_NL = array(
	'MSC Post Expiry' => 'MSC Post Expiry',
	'https://anomalous.co.za' => 'https://anomalous.co.za',
	'Automatically expire posts and pages on a scheduled date. Set expiration dates in the post editor and let the plugin handle the rest.' => 'Berichten en pagina\'s automatisch laten verlopen op een geplande datum. Stel vervaldata in de berichteneditor in en laat de plugin de rest afhandelen.',
	'Anomalous Developers' => 'Anomalous Developers',
	'You do not have permission to access this page.' => 'U heeft geen toestemming om deze pagina te openen.',
	'Security check failed. Please try again.' => 'Beveiligingscontrole mislukt. Probeer het opnieuw.',
	'Settings' => 'Instellingen',
	'Support' => 'Ondersteuning',
	'Settings saved.' => 'Instellingen opgeslagen.',
	'Move to Trash' => 'Naar prullenbak verplaatsen',
	'Permanently Delete' => 'Permanent verwijderen',
	'Change to Draft' => 'Wijzigen naar concept',
	'Change to Private' => 'Wijzigen naar privé',
	'Move to Category' => 'Verplaatsen naar categorie',
	'Enable post expiry' => 'Berichtvervaldatum inschakelen',
	'Allow posts to expire on a scheduled date.' => 'Berichten laten verlopen op een geplande datum.',
	'Post type mode' => 'Berichttype-modus',
	'Enable expiry only on selected post types' => 'Vervaldatum alleen inschakelen voor geselecteerde berichttypen',
	'Enable expiry on all public post types except selected' => 'Vervaldatum inschakelen voor alle openbare berichttypen behalve geselecteerde',
	'Post types' => 'Berichttypen',
	'Expiry action' => 'Vervalactie',
	'What should happen when a post expires.' => 'Wat er moet gebeuren wanneer een bericht verloopt.',
	'Expiry category' => 'Vervalcategorie',
	'Select a category' => 'Selecteer een categorie',
	'Posts will be moved to this category when expired. Only used when "Move to Category" is selected above.' => 'Berichten worden bij verloop naar deze categorie verplaatst. Wordt alleen gebruikt wanneer hierboven «Verplaatsen naar categorie» is geselecteerd.',
	'Save Settings' => 'Instellingen opslaan',
	'How to Use Post Expiry' => 'Hoe de berichtvervaldatum te gebruiken',
	'Post Expiry allows you to automatically handle posts when they reach a specified expiration date.' => 'Met de berichtvervaldatum kunt u berichten automatisch beheren wanneer ze een opgegeven vervaldatum bereiken.',
	'Setting an Expiry Date' => 'Een vervaldatum instellen',
	'When editing a post or page, look for the "Post Expiry" box in the sidebar on the right. Enter the date and time when you want the post to expire.' => 'Zoek bij het bewerken van een bericht of pagina naar het vak «Berichtvervaldatum» in de zijbalk rechts. Voer de datum en tijd in waarop u het bericht wilt laten verlopen.',
	'Expiry Actions' => 'Vervalacties',
	'When a post expires, one of the following actions will occur based on your settings:' => 'Wanneer een bericht verloopt, wordt een van de volgende acties uitgevoerd op basis van uw instellingen:',
	'The post is moved to trash and no longer visible to visitors.' => 'Het bericht wordt naar de prullenbak verplaatst en is niet meer zichtbaar voor bezoekers.',
	'The post is permanently deleted from your site.' => 'Het bericht wordt permanent van uw site verwijderd.',
	'The post is changed to draft status and hidden from visitors.' => 'Het bericht wordt gewijzigd naar conceptstatus en verborgen voor bezoekers.',
	'The post is changed to private status and only visible to logged-in users with appropriate permissions.' => 'Het bericht wordt gewijzigd naar privéstatus en is alleen zichtbaar voor ingelogde gebruikers met de juiste rechten.',
	'The post is moved to a specific archive category. Configure the category in the Settings tab.' => 'Het bericht wordt verplaatst naar een specifieke archiefcategorie. Configureer de categorie op het tabblad Instellingen.',
	'Post Type Configuration' => 'Berichttype-configuratie',
	'Use the Settings tab to choose which post types support expiry dates. You can either enable expiry on specific post types or disable it on specific types while enabling it on all others.' => 'Gebruik het tabblad Instellingen om te kiezen welke berichttypen vervaldata ondersteunen. U kunt vervaldatums inschakelen voor specifieke berichttypen of uitschakelen voor specifieke typen terwijl het is ingeschakeld voor alle andere.',
	'Frequently Asked Questions' => 'Veelgestelde vragen',
	'The Post Expiry metabox is not showing on my posts.' => 'De berichtvervaldatum-metabox wordt niet weergegeven bij mijn berichten.',
	'Check that "Enable post expiry" is ticked on the Settings tab.' => 'Controleer of «Berichtvervaldatum inschakelen» is aangevinkt op het tabblad Instellingen.',
	'Check that the post type (e.g. Post, Page) is selected in the Post types list.' => 'Controleer of het berichttype (bijv. Bericht, Pagina) is geselecteerd in de berichttypelijst.',
	'The metabox appears in the sidebar on the right when editing a post.' => 'De metabox verschijnt in de zijbalk rechts bij het bewerken van een bericht.',
	'When does the expiry action occur?' => 'Wanneer wordt de vervalactie uitgevoerd?',
	'Post expiry is processed by WordPress scheduled events (cron). The action will occur shortly after the expiry date and time passes. The exact timing depends on your site traffic and WordPress cron configuration.' => 'De berichtvervaldatum wordt verwerkt door geplande WordPress-gebeurtenissen (cron). De actie wordt kort na de vervaldatum en -tijd uitgevoerd. De exacte timing hangt af van uw siteverkeer en WordPress-cronconfiguratie.',
	'Can I disable expiry for a specific post?' => 'Kan ik de vervaldatum voor een specifiek bericht uitschakelen?',
	'Yes. Simply leave the expiry date and time fields empty in the Post Expiry metabox.' => 'Ja. Laat gewoon de velden voor vervaldatum en -tijd leeg in de berichtvervaldatum-metabox.',
	'Need Help?' => 'Hulp nodig?',
	'If you have questions, encounter bugs, or need setup assistance, we\'re here to help.' => 'Als u vragen heeft, bugs vindt of hulp nodig heeft bij de installatie, staan we voor u klaar.',
	'Get Support' => 'Ondersteuning krijgen',
	'Post Expiry' => 'Berichtvervaldatum',
	'Expiry Date' => 'Vervaldatum',
	'Expiry Time' => 'Vervaltijd',
	'Leave empty to disable expiry for this post.' => 'Laat leeg om de vervaldatum voor dit bericht uit te schakelen.',
	'Every 5 minutes' => 'Elke 5 minuten',
	'No expiry set' => 'Geen vervaldatum ingesteld',
	'Expired' => 'Verlopen',
	'Expires soon' => 'Verloopt binnenkort',
);

$nl_NL_plural = array(
	'%d day remaining' => array( '%d dag resterend', '%d dagen resterend' ),
	'%d hour remaining' => array( '%d uur resterend', '%d uur resterend' ),
);

// Portuguese (Brazil)
$pt_BR = array(
	'MSC Post Expiry' => 'MSC Post Expiry',
	'https://anomalous.co.za' => 'https://anomalous.co.za',
	'Automatically expire posts and pages on a scheduled date. Set expiration dates in the post editor and let the plugin handle the rest.' => 'Expirar automaticamente posts e páginas em uma data programada. Defina datas de expiração no editor de posts e deixe o plugin cuidar do resto.',
	'Anomalous Developers' => 'Anomalous Developers',
	'You do not have permission to access this page.' => 'Você não tem permissão para acessar esta página.',
	'Security check failed. Please try again.' => 'A verificação de segurança falhou. Por favor, tente novamente.',
	'Settings' => 'Configurações',
	'Support' => 'Suporte',
	'Settings saved.' => 'Configurações salvas.',
	'Move to Trash' => 'Mover para a lixeira',
	'Permanently Delete' => 'Excluir permanentemente',
	'Change to Draft' => 'Alterar para rascunho',
	'Change to Private' => 'Alterar para privado',
	'Move to Category' => 'Mover para categoria',
	'Enable post expiry' => 'Ativar expiração de posts',
	'Allow posts to expire on a scheduled date.' => 'Permitir que posts expirem em uma data programada.',
	'Post type mode' => 'Modo de tipo de post',
	'Enable expiry only on selected post types' => 'Ativar expiração apenas nos tipos de post selecionados',
	'Enable expiry on all public post types except selected' => 'Ativar expiração em todos os tipos de post públicos exceto os selecionados',
	'Post types' => 'Tipos de post',
	'Expiry action' => 'Ação de expiração',
	'What should happen when a post expires.' => 'O que deve acontecer quando um post expira.',
	'Expiry category' => 'Categoria de expiração',
	'Select a category' => 'Selecionar uma categoria',
	'Posts will be moved to this category when expired. Only used when "Move to Category" is selected above.' => 'Os posts serão movidos para esta categoria quando expirarem. Usado apenas quando «Mover para categoria» é selecionado acima.',
	'Save Settings' => 'Salvar configurações',
	'How to Use Post Expiry' => 'Como usar a expiração de posts',
	'Post Expiry allows you to automatically handle posts when they reach a specified expiration date.' => 'A expiração de posts permite gerenciar automaticamente os posts quando atingem uma data de expiração especificada.',
	'Setting an Expiry Date' => 'Definir uma data de expiração',
	'When editing a post or page, look for the "Post Expiry" box in the sidebar on the right. Enter the date and time when you want the post to expire.' => 'Ao editar um post ou página, procure a caixa «Expiração do post» na barra lateral à direita. Insira a data e hora em que deseja que o post expire.',
	'Expiry Actions' => 'Ações de expiração',
	'When a post expires, one of the following actions will occur based on your settings:' => 'Quando um post expira, uma das seguintes ações será realizada com base nas suas configurações:',
	'The post is moved to trash and no longer visible to visitors.' => 'O post é movido para a lixeira e não é mais visível para os visitantes.',
	'The post is permanently deleted from your site.' => 'O post é excluído permanentemente do seu site.',
	'The post is changed to draft status and hidden from visitors.' => 'O post é alterado para o status de rascunho e ocultado dos visitantes.',
	'The post is changed to private status and only visible to logged-in users with appropriate permissions.' => 'O post é alterado para o status privado e visível apenas para usuários conectados com as permissões apropriadas.',
	'The post is moved to a specific archive category. Configure the category in the Settings tab.' => 'O post é movido para uma categoria de arquivo específica. Configure a categoria na aba Configurações.',
	'Post Type Configuration' => 'Configuração de tipos de post',
	'Use the Settings tab to choose which post types support expiry dates. You can either enable expiry on specific post types or disable it on specific types while enabling it on all others.' => 'Use a aba Configurações para escolher quais tipos de post suportam datas de expiração. Você pode ativar a expiração em tipos específicos ou desativá-la em tipos específicos enquanto a ativa em todos os outros.',
	'Frequently Asked Questions' => 'Perguntas frequentes',
	'The Post Expiry metabox is not showing on my posts.' => 'A metabox de expiração não está aparecendo nos meus posts.',
	'Check that "Enable post expiry" is ticked on the Settings tab.' => 'Verifique se «Ativar expiração de posts» está marcado na aba Configurações.',
	'Check that the post type (e.g. Post, Page) is selected in the Post types list.' => 'Verifique se o tipo de post (ex. Post, Página) está selecionado na lista de tipos de post.',
	'The metabox appears in the sidebar on the right when editing a post.' => 'A metabox aparece na barra lateral à direita ao editar um post.',
	'When does the expiry action occur?' => 'Quando a ação de expiração é executada?',
	'Post expiry is processed by WordPress scheduled events (cron). The action will occur shortly after the expiry date and time passes. The exact timing depends on your site traffic and WordPress cron configuration.' => 'A expiração de posts é processada por eventos agendados do WordPress (cron). A ação será executada logo após a data e hora de expiração. O momento exato depende do tráfego do seu site e da configuração do cron do WordPress.',
	'Can I disable expiry for a specific post?' => 'Posso desativar a expiração para um post específico?',
	'Yes. Simply leave the expiry date and time fields empty in the Post Expiry metabox.' => 'Sim. Simplesmente deixe os campos de data e hora de expiração vazios na metabox de expiração.',
	'Need Help?' => 'Precisa de ajuda?',
	'If you have questions, encounter bugs, or need setup assistance, we\'re here to help.' => 'Se você tem perguntas, encontrou bugs ou precisa de ajuda com a configuração, estamos aqui para ajudar.',
	'Get Support' => 'Obter suporte',
	'Post Expiry' => 'Expiração do post',
	'Expiry Date' => 'Data de expiração',
	'Expiry Time' => 'Hora de expiração',
	'Leave empty to disable expiry for this post.' => 'Deixe vazio para desativar a expiração deste post.',
	'Every 5 minutes' => 'A cada 5 minutos',
	'No expiry set' => 'Sem expiração definida',
	'Expired' => 'Expirado',
	'Expires soon' => 'Expira em breve',
);

$pt_BR_plural = array(
	'%d day remaining' => array( '%d dia restante', '%d dias restantes' ),
	'%d hour remaining' => array( '%d hora restante', '%d horas restantes' ),
);

// Portuguese (Portugal)
$pt_PT = array(
	'MSC Post Expiry' => 'MSC Post Expiry',
	'https://anomalous.co.za' => 'https://anomalous.co.za',
	'Automatically expire posts and pages on a scheduled date. Set expiration dates in the post editor and let the plugin handle the rest.' => 'Expirar automaticamente publicações e páginas numa data programada. Defina datas de expiração no editor de publicações e deixe o plugin tratar do resto.',
	'Anomalous Developers' => 'Anomalous Developers',
	'You do not have permission to access this page.' => 'Não tem permissão para aceder a esta página.',
	'Security check failed. Please try again.' => 'A verificação de segurança falhou. Por favor, tente novamente.',
	'Settings' => 'Definições',
	'Support' => 'Suporte',
	'Settings saved.' => 'Definições guardadas.',
	'Move to Trash' => 'Mover para o lixo',
	'Permanently Delete' => 'Eliminar permanentemente',
	'Change to Draft' => 'Alterar para rascunho',
	'Change to Private' => 'Alterar para privado',
	'Move to Category' => 'Mover para categoria',
	'Enable post expiry' => 'Ativar expiração de publicações',
	'Allow posts to expire on a scheduled date.' => 'Permitir que publicações expirem numa data programada.',
	'Post type mode' => 'Modo de tipo de publicação',
	'Enable expiry only on selected post types' => 'Ativar expiração apenas nos tipos de publicação selecionados',
	'Enable expiry on all public post types except selected' => 'Ativar expiração em todos os tipos de publicação públicos exceto os selecionados',
	'Post types' => 'Tipos de publicação',
	'Expiry action' => 'Ação de expiração',
	'What should happen when a post expires.' => 'O que deve acontecer quando uma publicação expira.',
	'Expiry category' => 'Categoria de expiração',
	'Select a category' => 'Selecionar uma categoria',
	'Posts will be moved to this category when expired. Only used when "Move to Category" is selected above.' => 'As publicações serão movidas para esta categoria quando expirarem. Usado apenas quando «Mover para categoria» é selecionado acima.',
	'Save Settings' => 'Guardar definições',
	'How to Use Post Expiry' => 'Como utilizar a expiração de publicações',
	'Post Expiry allows you to automatically handle posts when they reach a specified expiration date.' => 'A expiração de publicações permite gerir automaticamente as publicações quando atingem uma data de expiração especificada.',
	'Setting an Expiry Date' => 'Definir uma data de expiração',
	'When editing a post or page, look for the "Post Expiry" box in the sidebar on the right. Enter the date and time when you want the post to expire.' => 'Ao editar uma publicação ou página, procure a caixa «Expiração da publicação» na barra lateral à direita. Introduza a data e hora em que pretende que a publicação expire.',
	'Expiry Actions' => 'Ações de expiração',
	'When a post expires, one of the following actions will occur based on your settings:' => 'Quando uma publicação expira, uma das seguintes ações será realizada com base nas suas definições:',
	'The post is moved to trash and no longer visible to visitors.' => 'A publicação é movida para o lixo e deixa de ser visível para os visitantes.',
	'The post is permanently deleted from your site.' => 'A publicação é eliminada permanentemente do seu site.',
	'The post is changed to draft status and hidden from visitors.' => 'A publicação é alterada para o estado de rascunho e ocultada dos visitantes.',
	'The post is changed to private status and only visible to logged-in users with appropriate permissions.' => 'A publicação é alterada para o estado privado e visível apenas para utilizadores autenticados com as permissões apropriadas.',
	'The post is moved to a specific archive category. Configure the category in the Settings tab.' => 'A publicação é movida para uma categoria de arquivo específica. Configure a categoria no separador Definições.',
	'Post Type Configuration' => 'Configuração de tipos de publicação',
	'Use the Settings tab to choose which post types support expiry dates. You can either enable expiry on specific post types or disable it on specific types while enabling it on all others.' => 'Utilize o separador Definições para escolher quais tipos de publicação suportam datas de expiração. Pode ativar a expiração em tipos específicos ou desativá-la em tipos específicos enquanto a ativa em todos os outros.',
	'Frequently Asked Questions' => 'Perguntas frequentes',
	'The Post Expiry metabox is not showing on my posts.' => 'A metabox de expiração não está a aparecer nas minhas publicações.',
	'Check that "Enable post expiry" is ticked on the Settings tab.' => 'Verifique se «Ativar expiração de publicações» está assinalado no separador Definições.',
	'Check that the post type (e.g. Post, Page) is selected in the Post types list.' => 'Verifique se o tipo de publicação (ex. Publicação, Página) está selecionado na lista de tipos de publicação.',
	'The metabox appears in the sidebar on the right when editing a post.' => 'A metabox aparece na barra lateral à direita ao editar uma publicação.',
	'When does the expiry action occur?' => 'Quando é executada a ação de expiração?',
	'Post expiry is processed by WordPress scheduled events (cron). The action will occur shortly after the expiry date and time passes. The exact timing depends on your site traffic and WordPress cron configuration.' => 'A expiração de publicações é processada por eventos agendados do WordPress (cron). A ação será executada logo após a data e hora de expiração. O momento exato depende do tráfego do seu site e da configuração do cron do WordPress.',
	'Can I disable expiry for a specific post?' => 'Posso desativar a expiração para uma publicação específica?',
	'Yes. Simply leave the expiry date and time fields empty in the Post Expiry metabox.' => 'Sim. Simplesmente deixe os campos de data e hora de expiração vazios na metabox de expiração.',
	'Need Help?' => 'Precisa de ajuda?',
	'If you have questions, encounter bugs, or need setup assistance, we\'re here to help.' => 'Se tem perguntas, encontrou bugs ou precisa de ajuda com a configuração, estamos aqui para ajudar.',
	'Get Support' => 'Obter suporte',
	'Post Expiry' => 'Expiração da publicação',
	'Expiry Date' => 'Data de expiração',
	'Expiry Time' => 'Hora de expiração',
	'Leave empty to disable expiry for this post.' => 'Deixe vazio para desativar a expiração desta publicação.',
	'Every 5 minutes' => 'A cada 5 minutos',
	'No expiry set' => 'Sem expiração definida',
	'Expired' => 'Expirado',
	'Expires soon' => 'Expira em breve',
);

$pt_PT_plural = array(
	'%d day remaining' => array( '%d dia restante', '%d dias restantes' ),
	'%d hour remaining' => array( '%d hora restante', '%d horas restantes' ),
);

// ----- Locale definitions -----

$locales = array(
	'de_DE' => array(
		'name'         => 'German (Germany)',
		'plural_forms' => 'nplurals=2; plural=(n != 1);',
		'translations' => $de_DE,
		'plurals'      => $de_DE_plural,
	),
	'de_CH' => array(
		'name'         => 'German (Switzerland)',
		'plural_forms' => 'nplurals=2; plural=(n != 1);',
		'translations' => $de_DE, // Swiss German uses same written standard.
		'plurals'      => $de_DE_plural,
	),
	'es_ES' => array(
		'name'         => 'Spanish (Spain)',
		'plural_forms' => 'nplurals=2; plural=(n != 1);',
		'translations' => $es_ES,
		'plurals'      => $es_ES_plural,
	),
	'es_MX' => array(
		'name'         => 'Spanish (Mexico)',
		'plural_forms' => 'nplurals=2; plural=(n != 1);',
		'translations' => $es_ES, // Latin American Spanish same for these strings.
		'plurals'      => $es_ES_plural,
	),
	'fr_FR' => array(
		'name'         => 'French (France)',
		'plural_forms' => 'nplurals=2; plural=(n > 1);',
		'translations' => $fr_FR,
		'plurals'      => $fr_FR_plural,
	),
	'fr_CA' => array(
		'name'         => 'French (Canada)',
		'plural_forms' => 'nplurals=2; plural=(n > 1);',
		'translations' => $fr_FR, // Canadian French same for these strings.
		'plurals'      => $fr_FR_plural,
	),
	'it_IT' => array(
		'name'         => 'Italian (Italy)',
		'plural_forms' => 'nplurals=2; plural=(n != 1);',
		'translations' => $it_IT,
		'plurals'      => $it_IT_plural,
	),
	'ja' => array(
		'name'         => 'Japanese',
		'plural_forms' => 'nplurals=1; plural=0;',
		'translations' => $ja,
		'plurals'      => $ja_plural,
	),
	'nl_NL' => array(
		'name'         => 'Dutch (Netherlands)',
		'plural_forms' => 'nplurals=2; plural=(n != 1);',
		'translations' => $nl_NL,
		'plurals'      => $nl_NL_plural,
	),
	'nl_BE' => array(
		'name'         => 'Dutch (Belgium)',
		'plural_forms' => 'nplurals=2; plural=(n != 1);',
		'translations' => $nl_NL, // Flemish uses same written standard.
		'plurals'      => $nl_NL_plural,
	),
	'pt_BR' => array(
		'name'         => 'Portuguese (Brazil)',
		'plural_forms' => 'nplurals=2; plural=(n != 1);',
		'translations' => $pt_BR,
		'plurals'      => $pt_BR_plural,
	),
	'pt_PT' => array(
		'name'         => 'Portuguese (Portugal)',
		'plural_forms' => 'nplurals=2; plural=(n != 1);',
		'translations' => $pt_PT,
		'plurals'      => $pt_PT_plural,
	),
);

// ----- Generate PO/MO files -----

foreach ( $locales as $locale_code => $locale_data ) {
	$po_file = $lang_dir . '/msc-post-expiry-' . $locale_code . '.po';

	// Build PO content.
	$po = "# Copyright (C) 2026 Anomalous Developers\n";
	$po .= "# This file is distributed under the GPL-2.0+ license.\n";
	$po .= "msgid \"\"\n";
	$po .= "msgstr \"\"\n";
	$po .= "\"Project-Id-Version: MSC Post Expiry 1.2.0\\n\"\n";
	$po .= "\"Report-Msgid-Bugs-To: https://wordpress.org/support/plugin/msc-post-expiry\\n\"\n";
	$po .= "\"POT-Creation-Date: 2026-04-25T09:56:29+00:00\\n\"\n";
	$po .= "\"PO-Revision-Date: 2026-04-25 10:00:00+00:00\\n\"\n";
	$po .= "\"Last-Translator: Anomalous Developers <dev@anomalous.co.za>\\n\"\n";
	$po .= "\"Language-Team: " . $locale_data['name'] . " <LL@li.org>\\n\"\n";
	$po .= "\"Language: " . $locale_code . "\\n\"\n";
	$po .= "\"MIME-Version: 1.0\\n\"\n";
	$po .= "\"Content-Type: text/plain; charset=UTF-8\\n\"\n";
	$po .= "\"Content-Transfer-Encoding: 8bit\\n\"\n";
	$po .= "\"Plural-Forms: " . $locale_data['plural_forms'] . "\\n\"\n";
	$po .= "\"X-Generator: scripts/generate-translations.php\\n\"\n";
	$po .= "\n";

	$translations = $locale_data['translations'];
	$plurals      = $locale_data['plurals'];
	$is_japanese  = ( 'ja' === $locale_code );

	foreach ( $entries as $entry ) {
		$msgid        = $entry['msgid'];
		$msgid_plural = $entry['msgid_plural'];

		if ( ! empty( $msgid_plural ) ) {
			// Plural entry.
			$po .= 'msgid "' . $msgid . '"' . "\n";
			$po .= 'msgid_plural "' . $msgid_plural . '"' . "\n";

			if ( isset( $plurals[ $msgid ] ) ) {
				if ( $is_japanese ) {
					$po .= 'msgstr[0] "' . addcslashes( $plurals[ $msgid ][0], '"' ) . '"' . "\n";
				} else {
					$po .= 'msgstr[0] "' . addcslashes( $plurals[ $msgid ][0], '"' ) . '"' . "\n";
					$po .= 'msgstr[1] "' . addcslashes( $plurals[ $msgid ][1], '"' ) . '"' . "\n";
				}
			} else {
				if ( $is_japanese ) {
					$po .= 'msgstr[0] ""' . "\n";
				} else {
					$po .= 'msgstr[0] ""' . "\n";
					$po .= 'msgstr[1] ""' . "\n";
				}
			}
		} else {
			// Singular entry.
			$po .= 'msgid "' . $msgid . '"' . "\n";
			if ( isset( $translations[ $msgid ] ) ) {
				$po .= 'msgstr "' . addcslashes( $translations[ $msgid ], '"' ) . '"' . "\n";
			} else {
				$po .= 'msgstr ""' . "\n";
			}
		}
		$po .= "\n";
	}

	file_put_contents( $po_file, $po );
	echo "Created: msc-post-expiry-{$locale_code}.po\n";

	// Compile MO file.
	$mo_file = $lang_dir . '/msc-post-expiry-' . $locale_code . '.mo';
	$cmd     = 'msgfmt -o ' . escapeshellarg( $mo_file ) . ' ' . escapeshellarg( $po_file ) . ' 2>&1';
	$output  = array();
	$ret     = 0;
	exec( $cmd, $output, $ret );

	if ( 0 === $ret ) {
		echo "Created: msc-post-expiry-{$locale_code}.mo\n";
	} else {
		echo "ERROR compiling MO for {$locale_code}: " . implode( "\n", $output ) . "\n";
	}
}

echo "\nDone! Generated translations for " . count( $locales ) . " locales.\n";
