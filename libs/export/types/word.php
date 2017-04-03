<?php 
class AcreatExportWORD extends AcreatExport
{
	/* ---
	* EXPORT
	*/
	function export($modele = "") 
	{
		$CSV_LINES = $this->_generate_csv_data();
		
		// Support des modele chemin réseau
		if(ereg("^\\\\\\\\",$modele))
			$modele = stripslashes($modele);
			
		print "<SCRIPT LANGUAGE=VBScript>
			
			Dim nomModele 
			Dim requete
			Dim oApp
			Dim oDoc
			Dim oMergedDoc
			Dim sty
			Dim ils
			
			Dim imageListWidth()
			Dim imageListHeight()
			
			Dim varAnswer
			
			Dim WIZARD
			
			On Error Resume Next
			Set oApp = GetObject(, \"Word.Application\")
			If Err <> 0 then
				' If GetObject fails, then use CreateObject instead.
				Set oApp = CreateObject(\"Word.Application\")
			End If
			
			'Sub oApp_MailMergeWizardStateChange(Doc, FromState, ToState, Handled)
				'MsgBox FromState 
			'End sub

			WIZARD = False
			Set regEx = New RegExp   ' Create a regular expression.
			regEx.Pattern = \"[0-9]+\"   ' Set pattern.
			regEx.IgnoreCase = True   ' Set case insensitivity.
			Set Matches = regEx.Execute(oApp.Version) ' Execute search. 
			VERSION = CInt(Matches(0))
			If VERSION > 9 then
				WIZARD = True
			End If
			
			oApp.Visible = false
			On Error Resume Next
			
			Set fso = CreateObject(\"Scripting.FileSystemObject\")
			Const TemporaryFolder = 2
			tempFileName = fso.GetSpecialFolder(TemporaryFolder) & \"\~acreattmp".time().".csv\"

			If tempFileName = \"\" Then
				MsgBox \"ATTENTION ! Vous devez être en site de confiance pour faire fonctionner le publipostage !\"
			End if

			Set oDoc = oApp.Documents.Add
			";
			
			$CSV_LINES = array_reverse($CSV_LINES);
			foreach($CSV_LINES as $key=>$line) {
				$line = preg_replace('/\"/','" & Chr(34) & "',$line);
				$line = preg_replace('/\r\n|\r|\n/',' " & Chr(13) & "',$line);
				print 'oApp.Selection.Range.Text = "'.$line.'"' . ( $key ? ' & Chr(13)' : '') . "\r\n";				
			}
				
			print "
			oApp.ActiveDocument.SaveAs tempFileName
			oApp.ActiveDocument.Close
			
			oApp.Visible = true
			
			nomModele = \"".$modele."\"
			If nomModele = \"\" then
				oApp.Documents.Add
				Set oDoc = oApp.ActiveDocument
				With oDoc.MailMerge
					.OpenDataSource tempFileName
					If WIZARD Then
						.ShowWizard 1, True, True, False, True, True, True
					End if
				End With
			Else
				oApp.Documents.Open nomModele
				Set oDoc = oApp.ActiveDocument
				With oDoc.MailMerge
					.OpenDataSource tempFileName
					If WIZARD Then
						.ShowWizard 1, True, False, False, False, True, True
					Else
						.Execute(true)
					End if
				End With
				
				If not WIZARD Then
					oDoc.Close False
				End if
				
				ReDim imageListWidth(oApp.ActiveDocument.InlineShapes.Count)
				ReDim imageListHeight(oApp.ActiveDocument.InlineShapes.Count)			
				
				compteur = 0
				For Each ils In oApp.ActiveDocument.InlineShapes
					 imageListWidth(compteur) = ils.Width
					 imageListHeight(compteur) = ils.Height
					 compteur = compteur + 1 
				 Next
				 
				 oApp.ActiveDocument.Fields.Update
			End If
			oApp.Visible = True
			
		</SCRIPT>"; 
		exit;
	}
	
	/* -----------------------------
	* _generate_tmp_cvs
	*/
	function _generate_csv_data()
	{
		$CSV = array();
		
		if( $this->_cols )
			$CSV[] = "\"" . implode("\";\"",$this->_cols)."\"";
		
		while($row = $this->_fetch() ) {
			$INFOS = array();
			foreach( $this->_cols as $key => $col ) {
				$INFOS[$key] = preg_replace('/\"/','""', @$row[$key]);
			}
			$CSV[] = '"' . implode('";"',$INFOS).'"';
		}
		
		return $CSV;
	}
	
}
?>