<?php
    /**
     * Dependencies
     */
    require_once ICMS_SYS_PATH . 'lib/models/stack.php';
    require_once ICMS_SYS_PATH . 'lib/view/templateparser/nodes/nodes.php';
    require_once ICMS_SYS_PATH . 'lib/view/templateparser/nodes/nodelist.php';
    
    /**
     *
     */
    class TemplateParser
    {
        protected $tags;
        protected $singleTags;

        public function __construct()
        {
            $this->tags = array();
            $this->singleTags = array();
        }

        public function setTags(array $tags)
        {
            $this->tags = $tags;
        }

        public function getTags()
        {
            return $this->tags;
        }

        public function setSingleTags(array $tags)
        {
            $this->singleTags = $tags;
        }

        public function getSingleTags()
        {
            return $this->singleTags;
        }

        protected function stripWhitespace($string)
        {
            return preg_replace(array('/>\s+/', '/\s+</', "/\r\n/"), array('>', '<', "\n"), $string);
        }
        
        public function parse($tpl)
        {
            $tags = array();

            $openTags = implode('|', array_keys($this->tags));
            $singleTags = implode('|', array_keys($this->singleTags));

            $this->getTagsByRegex($tags, $tpl, 'single', "/\{($singleTags)<([\w\d]+)>(?::([^\}]+))?\}/");

            $this->getTagsByRegex($tags, $tpl, 'open', "/\{($openTags)<([\w\d]+)>\}/");

            $this->getTagsByRegex($tags, $tpl, 'close', "/\{(?:\/($openTags))\}/");

            ksort($tags, SORT_NUMERIC);
            
            var_dump($tags);

            $root = new RootNode();
            $active =& $root;
            //$parent = null;

            $lastPos = 0;
            $tplLength = strlen($tpl);
            $tagsCount = count($tags);
            $tagStack = new Stack();

            foreach ($tags as $index => &$tag)
            {
                $text = trim(substr($tpl, $lastPos, $tag['pos'] - $lastPos));
                if (!empty($text))
                {
                    $text = $this->stripWhitespace($text);
                    $node = new TextNode($active->level + 1, $active);
                    $node->content = $text;
                    $active->children->addNode($node);
                }
                if ($tag['type'] == 'single')
                {
                    echo "single\n";
                    $active->children->addNode(new SingleNode(
                        $tags[$index]['name'],
                        $tags[$index]['index'],
                        $tags[$index]['data'],
                        $active->level + 1,
                        $active
                    ));
                }
                elseif ($tag['type'] == 'open')
                {
                    echo "open\n";
                    $active->children->addNode(new Node(
                        $tags[$index]['name'],
                        $tags[$index]['index'],
                        $active->level + 1,
                        $active
                    ));
                    $active =& $active->children->last;
                    $tagStack->push($tag['name']);
                }
                elseif ($tag['type'] == 'close')
                {
                    echo "close\n";
                    if ($tagStack->size() == 0)
                    {
                        throw new ViewException('[TemplateParser::parse] There was no open-tag for this close-tag: "{/' . $tag['name'] . '}"!', 401);
                    }
                    if ($tagStack->top() == $tag['name'])
                    {
                        $active =& $active->parent;
                        $tagStack->pop();
                    }
                }
                $lastPos = $tag['pos'] + $tag['length'];
            }
            if ($tagStack->size() > 0)
            {
                throw new ViewException('[TemplateParser::parse] There are unclosed tags!', 401);
            }

            $text = trim(substr($tpl, $lastPos));
            if (!empty($text))
            {
                $text = $this->stripWhitespace($text);
                $node = new TextNode($active->level + 1, $active);
                $node->content = $text;
                $active->children->addNode($node);
            }

            echo "\n\n\n\n";

            echo htmlspecialchars(print_r($root, true));

            echo "\n\n\n\n";

        }

        protected function getTagsByRegex(array& $array, &$tpl, $type, $regex)
        {
            preg_match_all($regex, $tpl, $matchesarray, PREG_OFFSET_CAPTURE);

            foreach ($matchesarray[0] as $index => $match)
            {
                $array[$match[1]] = array(
                    'type'      => $type,
                    'pos'       => $match[1],
                    'length'    => strlen($match[0]),
                    'name'      => $matchesarray[1][$index][0],
                    'index'     => (empty($matchesarray[2][$index][0]) ? null : $matchesarray[2][$index][0]),
                    'data'      => (empty($matchesarray[3][$index][0]) ? null : $matchesarray[3][$index][0])
                );
            }
        }
    }
?>
