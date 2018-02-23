<?php

namespace Core;

/*
|--------------------------------------------------------------------------
| Core : CustomExceptionMessage
|--------------------------------------------------------------------------
|
| customer exception message that has been throw
| it will return html/text back to the browser
|
*/

class CustomExceptionMessage
{
	function __construct($err) {
		$errMsg = "
		<style>
			body {
				background-color: #fff;
			}
			.container {
				width: 90%; 
				border: 1px dashed red;
				margin-right: auto;
				margin-left: auto;
				font-size: 1.3em;
				padding: 25 25 25 25;
				background-color: #F5F5DC;
				color: #333;
			}

			.sub-container {
				width: 90%; 
				border: 1px dashed black;
				margin-right: auto;
				margin-left: auto;
				font-size: 1.3em;
				padding: 25 25 25 25;
				background-color: #FBEEE6;
				color: #333;
				margin-bottom: 1px;
			}

			.fileline {
				padding-top: 15;
				color: #555;
			}

			.warningline {
				padding-top: 15;
				color: #555;
				font-size: 0.8em;
			}

			.error-order {
				font-weight: bold;
				font-size: 1.2em;
			}
		</style>
		";

		$file = $err->getFile();
		$traces = $err->getTrace();
		$code = $err->getCode();
		$line =  $err->getLine();
		$msg = $err->getMessage();

		$errMsg .=  "
			<div class='container'>
				<div>
					<strong>Error</strong> : ($code) $msg
				</div>
				<div class='fileline'>
					$file >>> Line $line
				</div>
			</div><hr>";

		$i = 1;
		foreach ($traces as $trace) {
			$errMsg .= " <div class='sub-container'> ";
			$errMsg .= " <div class='error-order'> #$i </div>";
			foreach ($trace as $key => $value) {
				if ($key !== 'args') {
					$errMsg .= " <div> <strong>$key</strong> : $value </div>";
				}
				else {
					$errMsg .= "<div class='warningline'>";
					foreach ($value as $index => $val) {
						$errMsg .= " <div> $val </div> ";
					}
					$errMsg .= "</div>";
				}
			}
			$errMsg .= " </div> ";
			$i++;
		}

    file_put_contents(__DIR__.'/log/PDOErrors.txt', $err.PHP_EOL.PHP_EOL, FILE_APPEND);
		die($errMsg);
	}
}