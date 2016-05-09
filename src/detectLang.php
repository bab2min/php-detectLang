<?php
/*
	detectLang: Language Detection Class(2016-04-27, Integralus Clarus)
*/
class detectLang {
	private $sql;
	private $langCode = array();

	static function splitWord($w) {
		$w = preg_replace('@[-!\@#$%^&*()_=+\\/`~[\\]{};:\'"<>,.???→。、，≪≫·]@ui', ' ', $w);
		return preg_split('/\s+/', $w, -1, PREG_SPLIT_NO_EMPTY);
	}
	
	static function mbStringToArray($string) { 
		while (mb_strlen($string)) { 
			$array[] = mb_substr($string, 0, 1, "UTF-8"); 
			$string = mb_substr($string, 1, NULL, "UTF-8"); 
		} 
		return $array; 
	}

	static function mbStringGram($string, $n = 3) {
		$string = '^'.$string.'$';
		while (mb_strlen($string) >= $n) { 
			$array[] = mb_substr($string, 0, $n, "UTF-8"); 
			$string = mb_substr($string, 1, NULL, "UTF-8"); 
		} 
		return $array; 
	}
	
	function loadLangCode() {
	  $handle = fopen(__DIR__ . '/../conf/langName.txt', "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $d = explode("\t", $line);
            $this->langCode[$d[0]] = $d[1];
        }
        fclose($handle);
    }
	}
	
	function __construct(){
	  $inf = parse_ini_file(__DIR__ . '/../conf/db.ini');
		$this->sql = new mysqli($inf['host'], $inf['id'], $inf['pw'], $inf['db']);
		$this->loadLangCode();
	}
	
	function detect($text, $max_result = 10) {
		// tokenize by space/special chrs
		$ws = self::splitWord($text);
		if(!$ws) return false;
		
		//make 3-gram array
		$tg = array();
		foreach($ws as $w) {
			$tg = array_merge($tg, self::mbStringGram($w));
		}
		$tg = array_unique($tg);

		// make 1-gram vector
		$c = array_count_values(self::mbStringToArray(strtolower(implode($ws))));
		$sc = sqrt(array_reduce($c, function($a, $x){return $a+$x*$x;}, 0));
		$c = array_map(function($t) use($sc){return $t/$sc;}, $c);

		// input 1-gram vector into tmp table
		$this->sql->query("CREATE TEMPORARY TABLE IF NOT EXISTS ugt (lchr VARCHAR(4), s DOUBLE, PRIMARY KEY (lchr)) ENGINE=Memory");
		$this->sql->query("TRUNCATE ugt");
		$qr = "INSERT INTO ugt VALUES ";
		foreach($c as $k=>$v) {
			$qr .= "('".$this->sql->real_escape_string($k)."',".$v."),";
		}
		$this->sql->query(substr($qr, 0, -1));
		
		// query similarity by languages
		$qr = "SELECT uni.lang code, uni.v * .5 + IFNULL(tri.v, 0) weight FROM "
				."(SELECT s.lang, SUM(s.sum*u.s) v FROM gram1_sum s "
				."INNER JOIN ugt u ON s.lchr = u.lchr "
				."WHERE s.lchr <> '' "
				."GROUP BY s.lang) uni "
			."LEFT JOIN (SELECT ts.lang, SUM(ts.sum) v FROM gram3_sum ts "
				."WHERE ts.lchr IN ('".implode("','", $tg)."') GROUP BY ts.lang) tri ON tri.lang = uni.lang "
			." ORDER BY weight DESC LIMIT {$max_result}";
		if(!($res = $this->sql->query($qr))) return false;
		
		$result = array();
		while($row = $res->fetch_assoc()) {
			$row['language'] = $this->langCode[$row['code']] ?? '';
			$result[] = $row;
		}
		return $result;
	}
}
?>
