<?php
    /**
     * 
     */
    abstract class Comments
    {
        /**
         * gets a single comment
         *
         * @static
         * @access public
         * @global Database $db
         * @param string $modul the owner modul
         * @param int $muid the modul unique identifier
         * @param int $guid the comment's ID
         * @return mixed the comment es an array or null
         */
        public static function get($modul, $muid, $guid)
        {
            try
            {
                global $db;
                $query = 'SELECT `guid`,`author`,`rawtext`,`parsedtext`,DATE_FORMAT(`datetime`, \'%d.%c.%Y\') AS \'date\' FROM `PREFIX_comments` WHERE `modul`=? AND `muid`=? AND `guid`=? LIMIT 1';
                $types = 'sii';
                $params = array($modul, $muid, $guid);
                $result = $db->GetData($query, $types, $params);
                if (count($result) > 0)
                {
                    $result = $result[0];
                    return array('guid' => $result->guid, 'author' => $result->author, 'raw' => $result->rawtext, 'parsed' => $result->parsedtext, 'date' => $result->date);
                }
                else
                {
                    return null;
                }
            }
            catch (DBException $e)
            {
                return false;
            }
        }

        /**
         * gets multiple comments as a multidimensional array
         *
         * @static
         * @access public
         * @global Database $db
         * @param string $modul the owner modul
         * @param int $muid the modul unique identifier
         * @param int $guid the comments' IDs
         * @return array the comments
         */
        public static function getMultiple($modul, $muid, $guids)
        {
            try
            {
                global $db;
                $query = 'SELECT `guid`,`author`,`rawtext`,`parsedtext`,DATE_FORMAT(`datetime`, \'%d.%c.%Y\') AS \'date\' FROM `PREFIX_comments` WHERE `modul`=? AND `muid`=? AND `guid`=? LIMIT 1';
                $types = 'sii';
                $params = array($modul, $muid);
                $comments = array();
                foreach ($guids as $guid)
                {
                    $params[2] = $guid;
                    $result = $db->GetData($query, $types, $params);
                    if (count($result) > 0)
                    {
                        $result = $result[0];
                        $comments[] = array('guid' => $result->guid, 'author' => $result->author, 'raw' => $result->rawtext, 'parsed' => $result->parsedtext, 'date' => $result->date);
                    }
                }
                return $comments;
            }
            catch (DBException $e)
            {
                return false;
            }
        }

        /**
         * gets a range of comments
         *
         * @static
         * @access public
         * @global Database $db
         * @param string $modul the owner modul
         * @param int $muid the modul unique identifier
         * @param int $offset the start position of the range
         * @param int $count the length of the range
         * @return array the comments
         */
        public static function getRange($modul, $muid, $offset, $count)
        {
            try
            {
                global $db;
                $query = 'SELECT `guid`,`author`,`rawtext`,`parsedtext`,DATE_FORMAT(`datetime`, \'%d.%c.%Y\') AS \'date\' FROM `PREFIX_comments` WHERE `modul`=? AND `muid`=? ORDER BY `datetime` DESC LIMIT ?,?';
                $types = 'siii';
                $params = array($modul, $muid, $offset, $count);
                $comments = array();
                foreach ($db->GetData($query, $types, $params) as $row)
                {
                    $comments[] = array('guid' => $row->guid, 'author' => $row->author, 'raw' => $row->rawtext, 'parsed' => $row->parsedtext, 'date' => $row->date);
                }
                return $comments;
            }
            catch (DBException $e)
            {
                return false;
            }
        }

        /**
         * gets all comments either from the whole owner modul or just from the MUID
         *
         * @static
         * @access public
         * @global Database $db
         * @param string $modul the owner modul
         * @param int $muid the modul unique identifier
         * @return array the comments
         */
        public static function getAll($modul, $muid = null)
        {
            try
            {
                global $db;
                $query = 'SELECT `guid`,`author`,`rawtext`,`parsedtext`,`parsedtext`,DATE_FORMAT(`datetime`, \'%d.%c.%Y\') AS \'date\' FROM `PREFIX_comments` WHERE `modul`=?';
                $types = 's';
                $params = array($modul);
                if (!is_null($muid))
                {
                    $query .= ' AND `muid`=?';
                    $types .= 'i';
                    $params[] = $muid;
                }
                $query .= ' ORDER BY `datetime` DESC';
                $comments = array();
                foreach ($db->GetData($query, $types, $params) as $row)
                {
                    $comments[] = array('guid' => $row->guid, 'author' => $row->author, 'raw' => $row->rawtext, 'parsed' => $row->parsedtext, 'date' => $row->date);
                }
                return $comments;
            }
            catch (DBException $e)
            {
                return false;
            }
        }

        /**
         * adds a comment with the given data
         *
         * @static
         * @access public
         * @global Database $db
         * @param string $modul the owner modul
         * @param int $muid the modul unique identifier
         * @param string $author the comment author
         * @param string $rawtext the raw comment text
         * @param string $parsedtext the parsed comment text
         * @return bool true on success, false on error
         */
        public static function add($modul, $muid, $author, $rawtext, $parsedtext)
        {
            try
            {
                global $db;
                $query = 'INSERT INTO `PREFIX_comments` (`modul`,`muid`,`author`,`rawtext`,`parsedtext`,`datetime`) VALUES (?,?,?,?,?,NOW())';
                $types = 'sisss';
                $params = array($modul, $muid, $author, $rawtext, $parsedtext);
                $db->PushData($query, $types, $params);
                return true;
            }
            catch (DBException $e)
            {
                return false;
            }
        }

        /**
         * edits a comment
         *
         * @static
         * @access public
         * @global Database $db
         * @param string $modul the owner modul
         * @param int $muid the modul unique identifier
         * @param int $guid the comment's ID
         * @param string $author the comment author
         * @param string $rawtext the raw comment text
         * @param string $parsedtext the parsed comment text
         * @return bool true on success, otherwise false
         */
        public static function edit($modul, $muid, $guid, $author, $rawtext, $parsedtext)
        {
            try
            {
                global $db;
                $query = 'UPDATE `PREFIX_comments` SET `author`=?,`rawtext`=?,`parsedtext`=? WHERE `modul`=? AND `muid`=? AND `guid`=? LIMIT 1';
                $types = 'ssssii';
                $params = array($author, $rawtext, $parsedtext, $modul, $muid, $guid);
                $db->PushData($query, $types, $params);

                return $db->affected_rows;
            }
            catch (DBException $e)
            {
                return false;
            }
        }

        /**
         * deletes a single comment
         *
         * @static
         * @access public
         * @global Database $db
         * @param string $modul the owner modul
         * @param int $muid the modul unique identifier
         * @param int $guid the comment's ID
         * @return int the count of deleted comments
         */
        public static function delete($modul, $muid, $guid)
        {
            try
            {
                global $db;
                $query = 'DELETE FROM `PREFIX_comments` WHERE `modul`=? AND `muid`=? AND `guid`=? LIMIT 1';
                $types = 'sii';
                $params = array($modul, $muid, $guid);
                $db->PushData($query, $types, $params);
                return $db->affected_rows;
            }
            catch (DBException $e)
            {
                return false;
            }
        }

        /**
         * deletes multiple comments
         *
         * @static
         * @access public
         * @global Database $db
         * @param string $modul the owner modul
         * @param int $muid the modul unique identifier
         * @param int $guid the comment's ID
         * @return int the count of deleted comments
         */
        public static function deleteMultiple($modul, $muid, $guids)
        {
            try
            {
                global $db;
                $query = 'DELETE FROM `PREFIX_comments` WHERE `modul`=? AND `muid`=? AND `guid`=?';
                $types = 'sii';
                $params = array($modul, $muid);
                foreach ($guids as $guid)
                {
                    $params[] = $guid;
                    $db->PushData($query, $types, $params);
                }
                return $db->affected_rows;
            }
            catch (DBException $e)
            {
                return false;
            }
        }

        /**
         * deletes a range of comments
         *
         * @static
         * @access public
         * @global Database $db
         * @param string $modul the owner modul
         * @param int $muid the modul unique identifier
         * @param int $offset the start position of the range
         * @param int $count the length of the range
         * @return int the count of deleted comments
         */
        public static function deleteRange($modul, $muid, $offset, $count)
        {
            try
            {
                global $db;
                $init = self::getRange($modul, $muid, $offset, 1);
                $init = $init[0]['guid'];
                $query = 'DELETE FROM `PREFIX_comments` WHERE `modul`=? AND `muid`=? AND `guid`<=? ORDER BY `guid` DESC LIMIT ?';
                $types = 'siii';
                $params = array($modul, $muid, $init, $count);
                $db->PushData($query, $types, $params);
                return $db->affected_rows;
            }
            catch (DBExcepton $e)
            {
                return false;
            }
        }

        /**
         * deletes all comments either from the whole owner modul or just from the MUID
         *
         * @static
         * @access public
         * @global Database $db
         * @param string $modul the owner modul
         * @param int $muid the modul unique identifier
         * @return int the count of deleted comments
         */
        public static function deleteAll($modul, $muid = null)
        {
            try
            {
                global $db;
                $query = 'DELETE FROM `PREFIX_comments` WHERE `modul`=?';
                $types = 's';
                $params = array($modul);
                if (!is_null($muid))
                {
                    $query .= ' AND `muid`=?';
                    $types .= 'i';
                    $params[] = $muid;
                }
                $db->PushData($query, $types, $params);
                return $db->affected_rows;
            }
            catch (DBException $e)
            {
                return false;
            }
        }

        /**
         * gets all moduls from the table
         *
         * @static
         * @access public
         * @global Database $db
         * @return array the moduls
         */
        public static function getModuls()
        {
            try
            {
                echo self::timestamp() . '<br />';
                global $db;
                $query = 'SELECT DISTINCT `modul` FROM `PREFIX_comments`';
                $moduls = array();
                foreach ($db->GetData($query) as $row)
                {
                    $moduls[] = $row->modul;
                }
                return $moduls;
            }
            catch (DBException $e)
            {
                return false;
            }
        }

        /**
         * counts the comments either from the whole owner modul or just from the MUID
         *
         * @static
         * @access public
         * @global Database $db
         * @param string $modul the owner modul
         * @param int $muid the modul unique identifier
         * @return int the count of the comments
         */
        public static function countComments($modul, $muid = null)
        {
            try
            {
                global $db;
                $query = 'SELECT count(`guid`) AS \'count\' FROM `PREFIX_comments` WHERE `modul`=?';
                $types = 's';
                $params = array($modul);
                if (!is_null($muid))
                {
                    $query .= ' AND `muid`=?';
                    $types .= 'i';
                    $params[] = $muid;
                }
                $result = $db->GetData($query, $types, $params);
                return $result[0]->count;
            }
            catch (DBException $e)
            {
                return false;
            }
        }
    }
?>
