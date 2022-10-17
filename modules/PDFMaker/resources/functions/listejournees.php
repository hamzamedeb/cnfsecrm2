<?php

if (!function_exists('pdfmakerListeJournees')) {

    function pdfmakerListeJournees($entityid) {
        global $adb;
        $query = "SELECT id,sequence_no,date_start,start_matin,end_matin,start_apresmidi,end_apresmidi,duree_formation
            FROM vtiger_inventorydatesrel
            WHERE vtiger_inventorydatesrel.id = ?";
        $result = $adb->pquery($query, array($entityid));
        $num_rows_dates = $adb->num_rows($result);
        $app = '<table border="0" cellpadding="0" cellspacing="0" width="100%" font-size: 14px;>
	<tbody>
		<tr>
			<td align="left" colspan="6" valign="middle" style="border:none;"><span style="font-size: 12px;">Calendrier des Journ&eacute;es</span></td>
		</tr>
		<tr 1px="" bgcolor="#b5bdcc" solid="">
			<td width="16%"><span style="font-size: 12px;">N&deg; Journ&eacute;e</span></td>
			<td width="16%"><span style="font-size: 12px;">Date</span></td>
			<td width="16%"><span style="font-size: 12px;">Heures matin</span></td>
			<td width="16%"><span style="font-size: 12px;">Heures apr&egrave;s-midi</span></td>
			<td width="16%"><span style="font-size: 12px;">Dur&eacute;e</span></td>
			<td width="20%"><span style="font-size: 12px;">Commentaire</span></td>
		</tr>';

        if ($num_rows_dates) {
            for ($i = 0; $i < $num_rows_dates; $i++) {
                $sequence_no = $adb->query_result($result, $i, 'sequence_no');
                $date_start = $adb->query_result($result, $i, 'date_start');
                $start_matin = $adb->query_result($result, $i, 'start_matin');
                $end_matin = $adb->query_result($result, $i, 'end_matin');
                $start_apresmidi = $adb->query_result($result, $i, 'start_apresmidi');
                $end_apresmidi = $adb->query_result($result, $i, 'end_apresmidi');
                $duree_formation = $adb->query_result($result, $i, 'duree_formation');
                if ($i % 2) {
                    $app .= '<tr 1px="" bgcolor="#d4e2ee" solid="">
			<td>' . $sequence_no . '</td>
			<td>' . $date_start . '</td>
			<td>De ' . $start_matin . ' &agrave; ' . $end_matin . '</td>
			<td>De ' . $start_apresmidi . ' &agrave; ' . $end_apresmidi . '</td>
			<td>' . $duree_formation . '</td>
			<td></td>
		</tr>';
                } else {
                    $app .= '<tr 1px="" bgcolor="#feffdd" solid="">
			<td>' . $sequence_no . '</td>
			<td>' . $date_start . '</td>
			<td>De ' . $start_matin . ' &agrave; ' . $end_matin . '</td>
			<td>De ' . $start_apresmidi . ' &agrave; ' . $end_apresmidi . '</td>
			<td>' . $duree_formation . '</td>
			<td></td>
		</tr>';
                }
            }
        }
        $app .= '</tbody>
</table>';
        return $app;
    }

}
