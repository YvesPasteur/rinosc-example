<?php
namespace Rinosc\RoutesBuilder;

use Symfony\Component\Finder\Finder;

class ClassFinder
{
    private $rootDirectory;
    private $rootNamespace;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @param string $rootDirectory
     * @param string $rootNamespace
     * @param Finder $finder
     */
    public function __construct($rootDirectory, $rootNamespace, Finder $finder)
    {
        $this->rootDirectory = $rootDirectory;

        $this->rootNamespace = $rootNamespace;

        $this->finder = $finder;
    }

    /**
     * @return array
     */
    public function getClasses()
    {
        $files = $this->getFilesCrawler();

        $classes = array();
        foreach ($files as $file) {
            $classes[] = $this->buildNamespace($file);
        }
        return $classes;
    }

    /**
     * @return Finder
     */
    private function getFilesCrawler()
    {
        return $this->finder->files()
            ->in($this->rootDirectory)
            ->name('/(Collection|Instance)Controller.php/')
            ->sort(
                // sort by directory level and controller type
                function (\SplFileInfo $a, \SplFileInfo $b) {
                    $aLevel = substr_count($a->getPath(), '/');
                    $bLevel = substr_count($b->getPath(), '/');

                    // on the same level CollectionController has precedence on InstanceController
                    if ($aLevel == $bLevel) {
                        if (strpos($b->getFilename(), 'Collection') === 0) {
                            return 1;
                        }
                    }

                    // on different levels, the lower has precedence on the higher
                    return $aLevel - $bLevel;
                }
            );
    }

    /**
     * @param $file
     * @return string
     */
    private function buildNamespace($file)
    {
        $classname = $file->getBasename('.php');


        $relativePath = $file->getRelativePath();
        if (! empty($relativePath)) {
            $relativePath = '/' . $relativePath;
        }

        $namespaceSuffix = str_replace('/', '\\', $relativePath . '/' . $classname);
        $namespace = $this->rootNamespace . $namespaceSuffix;

        return $namespace;
    }
}
