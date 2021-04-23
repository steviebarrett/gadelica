<?php

namespace models;

class sources {

  public static function getShortRef($id) {
    switch ($id) {
    	case "1":
    		return 'Eaglais na h-Alba';
    		break;
      case "2":
      	return 'Faclair Rianachd Phoblaich';
      	break;
      case "3":
      	return 'An Seotal';
      	break;
      case "4":
      	return 'Dualchas Nàdair na h-Alba';
      	break;
    	case "22":
    		return 'Dwelly';
    		break;
    	case "23":
    		return 'LEACAN supplement';
    		break;
    	default:
    		return '[unknown]';
    }
	}

  public static function getRef($id) {
    switch ($id) {
    	case "1":
    		return 'Eaglais na h-Alba – <em>Handbook of Biblical and Ecclesiastical Gaelic</em>';
    		break;
      case "2":
      	return 'Faclair Rianachd Phoblaich';
      	break;
      case "3":
      	return 'An Seotal';
      	break;
      case "4":
      	return 'Dualchas Nàdair na h-Alba – <em>Faclan Nàdair</em>';
      	break;
    	case "22":
    		return 'Dwelly – <em>Faclair Gàidhlig gu Beurla le Dealbhan</em>';
    		break;
    	case "23":
    		return 'LEACAN supplement to Dwelly';
    		break;
    	default:
    		return '[unknown]';
    }
	}

  public static function getEmoji($id) {
    switch ($id) {
      case "1":
        return '⛪️';
        break;
      case "2":
        return '📎';
        break;
      case "3":
        return '🎒';
        break;
      case "4":
        return '🦆';
        break;
      case "22":
        return '🧩';
        break;
      case "23":
        return '♒️';
        break;
      default:
        return '';
    }
  }

  public static function getExtLink($id) {
    switch ($id) {
    	case "1":
    		return 'https://www.churchofscotland.org.uk/__data/assets/pdf_file/0011/68708/ER-Gaelic-HANDBOOK-V5.pdf';
    		break;
      case "2":
    		return 'https://www.cne-siar.gov.uk/media/4714/gaelicenglish.pdf';
    		break;
    	default:
    		return '';
    }
	}

}
