<?php
    /**
     *
     */
    class TemplateParser
    {
        protected $tags;
        protected $singleTags;
        protected $preserveWhitespace;

        public function __construct()
        {
            $tags = glob(ICMS_SYS_PATH . 'lib/view/templateengine/tags/*tag.php');
            $count = count($tags);
            $map = array();
            for ($i = 0; $i < $count; $i++)
            {
                $tags[$i] = str_replace(ICMS_SYS_PATH, '', $tags[$i]);
                $class = ucfirst(preg_replace('/tag$/', 'Tag', strtolower(basename($tags[$i], '.php'))));
                $map[$class] = $tags[$i];
            }

            Autoloader::addClassMap($map);
            
            $this->tags = array(
                'ForEach' => new ForeachTag(),
                'If' => new IfTag()
            );
            $this->singleTags = array(
                'ViewHelper' => new ViewhelperTag(),
                'SubTemplate' => new SubtemplateTag(),
                'Widget' => new WidgetTag(),
                'Lang' => new LangTag()
            );
            $this->preserveWhitespace = false;
        }

        public function addTag($name, ITag $tag)
        {
            $this->tags['name'] = $tag;
        }

        public function getTag($name)
        {
            return $this->tag[$name];
        }

        public function addSingleTag($name, ITag $tag)
        {
            $this->singleTags[$name] = $tag;
        }

        public function getSingleTag($name)
        {
            return $this->singleTags[$name];
        }

        public function preserveWhitespace($preserve = null)
        {
            if ($preserve === null)
            {
                return $this->preserveWhitespace;
            }
            else
            {
                $this->preserveWhitespace = ($preserve ? true : false);
            }
        }

        protected function stripWhitespace($string)
        {
            if (!$this->preserveWhitespace)
            {
                $string = trim($string);
                $string = preg_replace(array('/>\s+/', '/\s+</', "/\r\n/"), array('>', '<', "\n"), $string);
            }
            return $string;
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
                $text = $this->stripWhitespace(substr($tpl, $lastPos, $tag['pos'] - $lastPos));
                if (!empty($text))
                {
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
                    $root->nodes[] =& $active->children->last;
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

            $text = $this->stripWhitespace(substr($tpl, $lastPos));
            if (!empty($text))
            {
                $node = new TextNode($active->level + 1, $active);
                $node->content = $text;
                $active->children->addNode($node);
            }

            //$root->register = array_merge($root->ifTags, $root->register, $root->forEachTags);

            echo "\n\n\n\n";

            //echo htmlspecialchars(print_r($root, true));

            $name = 'tpl_' . md5(1234123) . '.cached';
            file_put_contents($name, serialize($root));

            $root = unserialize(file_get_contents($name));

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
