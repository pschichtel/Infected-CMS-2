<?php

    /**
     * Dependencies
     */
    require_once ICMS_SYS_PATH . 'lib/text/text.php';
    require_once ICMS_SYS_PATH . 'lib/text/stringparser/stringparser.php';
    require_once ICMS_SYS_PATH . 'lib/text/geshi/geshi.php';
    //require_once ICMS_SYS_PATH . 'lib/text';
    
    /**
     *
     */
    class BBCode
    {
        private $bbcodes;
        private $smileys;
        private $lang;
        private $templates;

        /**
         * constructs the bbcode parser
         *
         * @access public
         * @param string[][] $bbcodes the bbcodes to user
         * @param string[] $smiles the smileys to use
         */
        public function __construct($bbcodes, $smileys = array(), $lang = array())
        {
            if (!is_array($bbcodes) || !is_array($smileys))
            {
                throw new Exception('BBCode::__construct: the bbcodes and smileys have to be passed as arrays!');
            }
            $this->bbcodes = $bbcodes;
            $this->smileys = $smileys;
            $this->lang = $lang;
        }

        /**
         * adds a template to the bbcode parser
         *
         * @access public
         * @param string $name
         * @param Template $template
         */
        public function addTemplate($name, Template $template)
        {
            $this->templates[$name] = $template;
        }

        /**
         * adds multiple tmeplates to the bbcode parser
         *
         * @access public
         * @param Template[] $templates
         */
        public function addTemplates($templates)
        {
            foreach ($template as $name => $template)
            {
                $this->addTemplate($name, $template);
            }
        }

        
        /**
         * parses all BBCodes, smiles, newlines and HTML special chars in $text
         *
         * @access public
         * @param string $text the text to parse
         * @param bool $chunk true to chunk long words
         * @param bool $bbcode true to parse BBCodes
         * @param bool $smiles true to parse smiles
         * @param array $disallowedBBCodes an array of disallowed BBCode-tags
         * @param bool $html true to allow HTML
         * @return string the parsed string
         */
        public function parse($text, $chunk = true, $bbcode = true, $smiles = true, $disallowedBBCodes = array(), $html = false)
        {
            $parser = new StringParser_BBCode();
            $parser->setGlobalCaseSensitive(false);
            $contenttypes = array('block', 'inline', 'listitem');
            if ($chunk && !$html)
            {
                $parser->addParser($contenttypes, array('Text', 'simpleChunk'));
            }
            if (!$html)
            {
                $parser->addParser($contenttypes, 'htmlspecialchars');
            }
            if ($smiles)
            {
                $parser->addParser($contenttypes, array($this, 'smilies'));
            }
            if (!$html)
            {
                $parser->addParser($contenttypes, array('Text', 'nl2br'));
            }
            if ($bbcode)
            {
                if (!in_array('list', $disallowedBBCodes))
                {
                    $parser->addCode('list', 'callback_replace', array($this, '_list'), array(), 'list', array('block', 'listitem'), array('inline'));
                    $parser->addCode('*', 'simple_replace', null, array('start_tag' => '<li>', 'end_tag' => '</li>'), 'listitem', array('list'), array());
                    $parser->setCodeFlag('*', 'closetag', BBCODE_CLOSETAG_OPTIONAL);
                    $parser->setCodeFlag('*', 'paragraphs', true);
                    $parser->setCodeFlag('list', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
                    $parser->setCodeFlag('list', 'opentag.before.newline', BBCODE_NEWLINE_DROP);
                    $parser->setCodeFlag('list', 'closetag.before.newline', BBCODE_NEWLINE_DROP);
                }

                foreach ($this->bbcodes as $code)
                {
                    if (in_array($code['name'], $disallowedBBCodes))
                    {
                        continue;
                    }
                    $params = array(
                        'start_tag' => $code['starttag'],
                        'end_tag' => $code['endtag']
                    );
                    $tmp = explode('&', $code['params']);
                    foreach ($tmp as $pair)
                    {
                        $parts = explode('=', $pair);
                        if (count($parts) < 2)
                        {
                            continue;
                        }
                        $values = explode(',', $parts[1]);
                        if (count($values) == 1)
                        {
                            $values = urldecode($values[0]);
                        }
                        else
                        {
                            $values = array_map('urldecode', $values);
                        }
                        $params[urldecode($parts[0])] = $values;
                    }
                    unset($tmp);
                    $parser->addCode(
                        $code['name'],
                        $code['parsetype'],
                        (preg_match('/(simple_replace|simple_replace_simgle)/i', $code['parsetype']) ? null : array($this, '_' . $code['name'])),
                        $params,
                        $code['contenttype'],
                        explode('|', $code['allowedin']),
                        explode('|', $code['notallowedin'])
                    );
                }
            }

            return $parser->parse($text);
        }
        
        /**
         * replaces the smiles in $text
         *
         * @access public
         * @param string $text the text to parse smilies in
         * @return string the parsed text
         */
        public function smilies($text, $smileys = array())
        {
            if (count($smileys) == 0)
            {
                $smileys = &$this->smileys;
            }
            foreach ($smileys as $smile => $src)
            {
                $replacement = '<img src="' . $src . '" class="smileys" alt="' . $smile . '" title="' . $smile . '" />';
                $text = preg_replace('/(\A|\s)' . preg_quote($smile, '/') . '(\Z|\s)/', '$1' . $replacement . '$2', $text);
            }
            return $text;
        }

        /**
         * strips bbcodes out of a string
         *
         * @global Database $db
         * @param string $text the string
         * @param bool $strict if true codes with strippable 1 are stripped too
         * @return string the stripped string
         */
        public function strip($text, $strict = false)
        {
            $parser = new StringParser_BBCode();
            $parser->setGlobalCaseSensitive(false);

            $parser->addCode('list', 'callback_replace', 'bbcode_list', array(), 'list', array('block', 'listitem'), array('inline'));
            $parser->addCode('*', 'simple_replace', null, array('start_tag' => '<li>', 'end_tag' => '</li>'), 'listitem', array('list'), array());
            $parser->setCodeFlag('*', 'closetag', BBCODE_CLOSETAG_OPTIONAL);
            $parser->setCodeFlag('*', 'paragraphs', true);
            $parser->setCodeFlag('list', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
            $parser->setCodeFlag('list', 'opentag.before.newline', BBCODE_NEWLINE_DROP);
            $parser->setCodeFlag('list', 'closetag.before.newline', BBCODE_NEWLINE_DROP);

            foreach ($this->bbcodes as $code)
            {
                if ($code['strippable'] === '0')
                {
                    continue;
                }
                elseif ($code['strippable'] === '1' && !$strict)
                {
                    continue;
                }
                $params = array(
                    'start_tag' => '',
                    'end_tag' => ''
                );
                $tmp = explode('&', $code['params']);
                foreach ($tmp as $pair)
                {
                    $parts = explode('=', $pair);
                    if (count($parts) < 2)
                    {
                        continue;
                    }
                    $values = explode(',', $parts[1]);
                    if (count($values) == 1)
                    {
                        $values = urldecode($values[0]);
                    }
                    else
                    {
                        $values = array_map('urldecode', $values);
                    }
                    $params[urldecode($parts[0])] = $values;
                }
                unset($tmp);
                $parser->addCode(
                    $code['name'],
                    $code['parsetype'],
                    (preg_match('/(simple_replace|simple_replace_simgle)/i', $code['parsetype']) ? null : array('BBCodes', '_strip')),
                    $params,
                    $code['contenttype'],
                    explode('|', $code['allowedin']),
                    explode('|', $code['notallowedin'])
                );
            }

            return $parser->parse($text);
        }
        public static function _url($action, $attributes, $content, $params, &$node_object)
        {
            if ($action == 'validate')
            {
                if (!isset($attributes['default']))
                {
                    $node_object->raw = $content;
                    return preg_match('/^[a-z\d]+?:\/\//si', $content);
                }
                return preg_match('/^[a-z]+?:\/\//si', $attributes['default']);
            }
            else
            {
                if (isset($attributes['default']))
                {
                    $name = $content;
                    $url = htmlspecialchars($attributes['default']);
                }
                else
                {
                    $name = $content;
                    $url = htmlspecialchars($node_object->raw);
                }
                return '<a class="external" href="' . $url . '" title="' . $url . '">' . $name . '</a>';
            }
        }

        public static function _quote($action, $attributes, $content, $params, &$node_object)
        {
            if ($action == 'validate')
            {
                return true;
            }
            else
            {

                /**
                 * STATISCHE AUSGABE
                 */
                if (isset($attributes['name']) && isset($attributes['date']))
                {
                    $name = isset($attributes['default']) ? $attributes['default'] : $attributes['name'];
                    return '<span>' . $name . ', ' . $attributes['date'] . '</span><hr />"' . $content . '"<hr />';
                }
                elseif (isset($attributes['default']) || isset($attributes['name']))
                {
                    $name = isset($attributes['default']) ? $attributes['default'] : $attributes['name'];
                    return '<span>' . $name . '</span><hr />"' . $content . '"<hr />';
                }
                else
                {
                    return '<hr />"' . $content . '"<hr />';
                }
            }
        }

        public static function _img($action, $attributes, $content, $params, &$node_object)
        {
            if ($action == 'validate')
            {
                return preg_match('/^[a-z]+?:\/\//si', $content);
            }
            else
            {
                return '<img src="' . htmlspecialchars($content) . '" class="pics" alt="" />';
            }
        }

        public static function _search($action, $attributes, $content, $params, &$node_object)
        {
            if ($action == 'validate')
            {
                return (isset($attributes['default']) || isset($attributes['provider']));
            }
            else
            {
                $provider;
                if (isset($attributes['default']))
                {
                    $provider = &$attributes['default'];
                }
                elseif (isset($attributes['provider']))
                {
                    $provider = &$attributes['provider'];
                }
                else
                {
                    $provider = 'google';
                }
                $query = urlencode($content);
                $content = htmlspecialchars($content);
                /**
                 * STATISCHE SPRACHE
                 */
                switch ($provider)
                {
                    case 'google':
                        return '<a class="external" href="http://www.google.com/search?q=' . $query . '">' . 'Suche' . ' @ Google: ' . $content . '</a>';
                    case 'yahoo':
                        return '<a class="external" href="http://search.yahoo.com/search?p=' . $query . '">' . 'Suche' . ' @ Yahoo: ' . $content . '</a>';
                    case 'bing':
                        return '<a class="external" href="http://www.bing.com/search?q=' . $query . '">' . 'Suche' . ' @ Bing: ' . $content . '</a>';
                    case 'lmgtfy':
                        return '<a class="external" href="http://lmgtfy.com/?q=' . $query . '">' . 'Suche' . ' @ LMGTFY: ' . $content . '</a>';
                    default:
                        return '<a class="external" href="http://www.google.com/search?q=' . $query . '">' . 'Suche' . ' @ Google: ' . $content . '</a>';
                }
            }
        }

        public static function _video($action, $attributes, $content, $params, &$node_object)
        {
            $vid = false;
            if (isset($attributes['default']) && preg_match('/^(google|youtube|metacafe|myvideo|vimeo)$/i', $attributes['default']))
            {
                $vid = true;
            }
            if ($action == 'validate')
            {
                if ($vid)
                {
                    return (preg_match('/^[\w\d-]+$/i', $content) && preg_match('/^(google|youtube|metacafe|myvideo|vimeo)$/i', $attributes['default']));
                }
                else
                {
                    $URL = parse_url($content);
                    if (!is_bool($URL) && isset($URL['host']) && (isset($URL['query']) || isset($URL['path'])))
                    {
                        return true;
                    }
                }
            }
            else
            {
                if ($vid)
                {
                    switch ($attributes['default'])
                    {
                        case 'google':
                            return '<embed id="VideoPlayback" src="http://video.google.com/googleplayer.swf?docid=' . $content . '&amp;hl=de&amp;fs=true" style="width:400px;height:326px" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash"></embed>';
                        case 'youtube':
                            return '<object width="560" height="340"><param name="movie" value="http://www.youtube.com/v/' . $content . '&amp;fs=1&amp;"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/' . $content . '&amp;fs=1&amp;" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="560" height="340"></embed></object>';
                        case 'myvideo':
                            return '<object style="width:470px;height:285px;" width="470" height="285"><param name="movie" value="http://www.myvideo.de/movie/' . $content . '"></param><param name="AllowFullscreen" value="true"></param><param name="AllowScriptAccess" value="always"></param><embed src="http://www.myvideo.de/movie/' . $content . '" width="470" height="285" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true"></embed></object>';
                        case 'metacafe':
                            return '<embed src="http://www.metacafe.com/fplayer/' . $content . '/video.swf" width="400" height="345" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always"></embed>';
                        case 'vimeo':
                            return '<object width="400" height="225"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=' . $content . '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=ff9933&amp;fullscreen=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id=' . $content . '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=ff9933&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="400" height="225"></embed></object>';
                        default:
                            /**
                             * STATISCHE SPRACE
                             */
                            return '<div>' . 'Das angegebene Video konnte nicht verarbeitet werden.' . '</div>';
                    }
                }
                else
                {
                    $URL = parse_url($content);
                    $host = &$URL['host'];
                    if (isset($URL['query']))
                    {
                        $QS;
                        mb_parse_str($URL['query'], $QS);
                        if (preg_match('/youtube/i', $host) && isset($QS['v']))
                        {
                            return '<object width="560" height="340"><param name="movie" value="http://www.youtube.com/v/' . $QS['v'] . '&amp;fs=1&amp;"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/' . $QS['v'] . '&amp;fs=1&amp;" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="560" height="340"></embed></object>';
                        }
                        elseif (preg_match('/video\.google/i', $host) && isset($QS['docid']))
                        {
                            return '<embed id="VideoPlayback" src="http://video.google.com/googleplayer.swf?docid=' . $QS['docid'] . '&amp;hl=de&amp;fs=true" style="width:400px;height:326px" allowFullScreen="true" allowScriptAccess="always" type="application/x-shockwave-flash"></embed>';
                        }
                    }
                    else
                    {
                        $paths = explode('/', $URL['path']);
                        if (preg_match('/myvideo/i', $host))
                        {
                            return '<object style="width:470px;height:285px;" width="470" height="285"><param name="movie" value="http://www.myvideo.de/movie/' . $paths[2] . '"></param><param name="AllowFullscreen" value="true"></param><param name="AllowScriptAccess" value="always"></param><embed src="http://www.myvideo.de/movie/' . $paths[2] . '" width="470" height="285" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true"></embed></object>';
                        }
                        elseif (preg_match('/metacafe/i', $host))
                        {
                            return '<embed src="http://www.metacafe.com/fplayer/' . $paths[2] . '/video.swf" width="400" height="345" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always"></embed>';
                        }
                        elseif (preg_match('/vimeo/i', $host))
                        {
                            return '<object width="400" height="225"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=' . $paths[1] . '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=ff9933&amp;fullscreen=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id=' . $paths[1] . '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=ff9933&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="400" height="225"></embed></object>';
                        }
                    }
                    /**
                     * STATISCHE SPRACHE
                     */
                    return '<div>' . 'Der gewünschte Video-Service wird nicht unterstützt.' . '</div>';
                }
            }
        }

        /**
         * @todo lang and template classes are used!
         */
        public static function _spoiler($action, $attributes, $content, $params, &$node_object)
        {
            if ($action == 'validate')
            {
                return true;
            }
            else
            {
                return $content;
                $lang = new Lang('other');
                $tpl = new Template('bbcode_spoiler', $lang, 4);
                $tpl->setParams(array('CONTENT' => $content));
                return $tpl->getPart(0, true);
            }
        }

        public static function _color($action, $attributes, $content, $params, &$node_object)
        {
            if ($action == 'validate')
            {
                if (!isset($attributes['default']))
                {
                    return false;
                }
                if (preg_match('/^[a-z]+$/i', $attributes['default']))
                {
                    return true;
                }
                if (preg_match('/^#([\da-f]{3}|[\da-f]{6})$/i', $attributes['default']))
                {
                    return true;
                }
                if (preg_match('/^rgb\(\d{1,3},\d{1,3},\d{1,3}\)$/i', $attributes['default']))
                {
                    return true;
                }
                return false;
            }
            else
            {
                return '<span style="color:' . $attributes['default'] . '">' . $content . '</span>';
            }
        }

        public static function _size($action, $attributes, $content, $params, &$node_object)
        {
            if ($action == 'validate')
            {
                return (preg_match('/^\d\d?$/', $attributes['default']) && (31 > intval($attributes['default'])));
            }
            else
            {
                return '<span style="font-size:' . $attributes['default'] . 'pt;">' . $content . '</span>';
            }
        }

        public static function _code($action, $attributes, $content, $params, &$node_object)
        {
            if ($action == 'validate')
            {
                if (isset($attributes['default']))
                {
                    $highlighter = new GeSHi($content, $attributes['default']);
                    if ($highlighter->error() === false)
                    {
                        $highlighter->enable_strict_mode(GESHI_MAYBE);
                        $highlighter->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
                        $node_object->highlighter = &$highlighter;
                        return true;
                    }
                    return false;
                }
                return true;
            }
            else
            {
                $content = trim($content, "\r\n");

                if (isset($attributes['default']))
                {
                    $content = $node_object->highlighter->parse_code();
                }
                else
                {
                    $content = htmlspecialchars($content);
                    $content = '<pre><ol><li>' . preg_replace("/\s*\n/", '</li><li>', $content) . '</li></ol></div>';
                }

                /**
                 * STATISCHE SPRACHE
                 */
                return '<div class="code"><h4>' . 'Code-Ansicht' . ':</h4>' . $content . '</div>';
            }
        }

        public static function _email($action, $attributes, $content, $params, &$node_object)
        {
            if ($action == 'validate')
            {
                return Text::is_email($content);
            }
            else
            {
               list($name, $host) = explode('@', $content);
               $name = htmlspecialchars(Text::simpleChunk(str_replace(array('_', '.', '-'), ' ', $name)));
               $host = Text::simpleChunk(str_replace('.', ' ', $host));
               $text = '[' . $name . ' et ' . $host . ']';
               $email = Text::entityEncode('mailto:' . $content);
               return '<a href="' . $email . '" class="email_address">' . $text . '</a>';
            }
        }

        public static function _list($action, $attributes, $content, $params, &$node_object)
        {
            if ($action == 'validate')
            {
                if (isset($attributes['default']))
                {
                    return preg_match('/[\w-]+/i', $attributes['default']);
                }
                return true;
            }
            else
            {
                if (isset($attributes['default']))
                {
                    return '<ul style="list-style-type:' . $attributes['default'] . ';">' . $content . '</ul>';
                }
                else
                {
                    return '<ul>' . $content . '</ul>';
                }
            }
        }

        public static function _font($action, $attributes, $content, $params, &$node_object)
        {
            if ($action == 'validate')
            {
                return preg_match('/^[a-z\d ]+$/i', $attributes['default']);
            }
            else
            {
                return '<span style="font-family:\'' . $attributes['default'] . '\';">' . $content . '</span>';
            }
        }

        public static function _strip($action, $attributes, $content, $params, &$node_object)
        {
            if ($action == 'validate')
            {
                return true;
            }
            else
            {
                return $content;
            }
        }
    }

?>
