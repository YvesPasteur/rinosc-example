<?php
namespace RinoscTests\RoutesBuilder;

use Rinosc\RoutesBuilder\ClassFinder;
use Symfony\Component\Finder\Finder;

class ClassFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The "/foo/bar" directory does not exist.
     */
    public function testGetClassesWithWrongRoot()
    {
        $rootDir = '/foo/bar';
        $classFinder = new ClassFinder($rootDir, 'Foo\Bar', new Finder());
        $classFinder->getClasses();
    }

    public function testGetClassesWithoutFiles()
    {
        $rootDir = $this->getFixturesRoot() . '/Empty';

        $classFinder = new ClassFinder($rootDir, 'Foo\Bar', new Finder());
        $this->assertEquals(array(), $classFinder->getClasses());
    }

    public function testGetClassesWithCollectionFile()
    {
        $rootDir = $this->getFixturesRoot() . '/SingleCollection';
        $rootNamespace = 'Root';
        $expectedClassname = array($rootNamespace . '\CollectionController');

        $classFinder = new ClassFinder($rootDir, $rootNamespace, new Finder());
        $this->assertEquals($expectedClassname, $classFinder->getClasses());
    }

    public function testGetClassesWithInstanceFile()
    {
        $rootDir = $this->getFixturesRoot() . '/SingleInstance';
        $rootNamespace = 'Root';
        $expectedClassname = array($rootNamespace . '\InstanceController');

        $classFinder = new ClassFinder($rootDir, $rootNamespace, new Finder());
        $this->assertEquals($expectedClassname, $classFinder->getClasses());
    }

    public function testGetClassesWithTwoLevels()
    {
        $rootDir = $this->getFixturesRoot() . '/TwoLevels';
        $rootNamespace = 'Root';
        $expectedClassname = array(
            $rootNamespace . '\CollectionController',
            $rootNamespace . '\InstanceController',
            $rootNamespace . '\Second\CollectionController',
            $rootNamespace . '\Second\InstanceController'
        );

        $classFinder = new ClassFinder($rootDir, $rootNamespace, new Finder());
        $this->assertEquals($expectedClassname, $classFinder->getClasses());
    }


    /**
     * @return string
     */
    private function getFixturesRoot()
    {
        return __DIR__ . '/Fixtures/ClassFinder';
    }
}
