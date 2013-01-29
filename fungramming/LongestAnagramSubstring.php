<?php
$long_str = 'FourscoreandsevenyearsagoourfaathersbroughtforthonthiscontainentanewnationconceivedinzLibertyanddedicatedtothepropositionthatallmenarecreatedequalNowweareengagedinagreahtcivilwartestingwhetherthatnaptionoranyynartionsoconceivedandsodedicatedcanlongendureWeareqmetonagreatbattlefiemldoftzhatwarWehavecometodedicpateaportionofthatfieldasafinalrestingplaceforthosewhoheregavetheirlivesthatthatnationmightliveItisaltogetherfangandproperthatweshoulddothisButinalargersensewecannotdedicatewecannotconsecratewecannothallowthisgroundThebravelmenlivinganddeadwhostruggledherehaveconsecrateditfaraboveourpoorponwertoaddordetractTgheworldadswfilllittlenotlenorlongrememberwhatwesayherebutitcanneverforgetwhattheydidhereItisforusthelivingrathertobededicatedheretotheulnfinishedworkwhichtheywhofoughtherehavethusfarsonoblyadvancedItisratherforustobeherededicatedtothegreattdafskremainingbeforeusthatfromthesehonoreddeadwetakeincreaseddevotiontothatcauseforwhichtheygavethelastpfullmeasureofdevotionthatweherehighlyresolvethatthesedeadshallnothavediedinvainthatthisnationunsderGodshallhaveanewbirthoffreedomandthatgovernmentofthepeoplebythepeopleforthepeopleshallnotperishfromtheearth';

$solution = Solution::main($long_str);
echo 'The longestAnagramSubstring is: '.$solution['str'].', start_index= '.$solution['start_index'];

class Solution
{
    public static function main($input_str)
    {
		$result_arr['str'] = '';
		$result_arr['start_index'] = 0;
		
		$arr = str_split($input_str);
		$len = count($arr);

		$substring = '';
		$longest_length = 0; 
		for ($i = 0; $i < $len; $i++) {
			$center = $i;
			
			//$isEvenLen indicate whether the considering substring is even/odd in length
			for ( $isEvenLen = 0; $isEvenLen <=1; $isEvenLen++ )
			{
				$substring = ''; $offset = 1;
				$stop = false;
				//start at center, traverse to both directions to find matching anagram
				while ( !$stop )
				{
					if ( $center-$offset < 0 || $center+$offset-$isEvenLen >= $len) 
					{
						$offset--;
						$stop = true;
					}
					else {
						if ($arr[$center-$offset] != $arr[$center+$offset-$isEvenLen]) 
						{	$offset--;
							$stop = true;
						}
						else $offset++;
					}
				}
				
				if ( $offset * 2+(1-$isEvenLen) > $longest_length )
				{
					$longest_length = $offset * 2+(1-$isEvenLen);
					$result_arr['str'] = substr($input_str, $center-$offset, $offset*2 + (1-$isEvenLen) );
					$result_arr['start_index'] = $center-$offset;
				}
			}
        }

        return $result_arr;
    }
}    
?>
