<?php
require_once(dirname(__FILE__)."/../bin/config.php");
require_once(WWW_DIR ."/lib/framework/db.php");
require_once(WWW_DIR."lib/category.php");
require_once(WWW_DIR."lib/groups.php");
require_once ("consoletools.php");
require_once ("namecleaner.php");

//this scripts is adapted from nZEDb decrypt_hashes.php

preName();

function preName()
{

    $db = new DB();
    $consoletools = new ConsoleTools();
    $counter = 0;
    $loops = 1;
	$reset = 0;


    $db->query("update releases set dehashstatus = -1 where dehashstatus = 0 and searchname REGEXP '[a-fA-F0-9]{32}'");
    if($res = $db->queryDirect("select ID, searchname from releases where dehashstatus between -6 and -1 and searchname REGEXP '[a-fA-F0-9]{32}'"))
	{
		foreach ($res as $row)
		{
			$success = false;
			if (preg_match('/([0-9a-fA-F]{32})/', $row['searchname'], $match))
			{
				if($res1 = $db->queryOneRow(sprintf("select releasename from prehash where hash = %s", $db->escapeString($match[1]))))
				{
					$db->query(sprintf("update releases set dehashstatus = 1, searchname = %s where ID = %d", $db->escapeString($res1['releasename']), $row['ID']));
					if ($db->getAffectedRows() >= 1)
					{
						$success = true;
						$counter++;
					}
				}
			}
			if ($success == false)
			$consoletools->overWrite("Renaming hashed releases:".$consoletools->percentString($loops++,mysqli_num_rows($res)));
		}
	}
	echo "\n".$counter. " release(s) names changed.\n";

    }


     
