<?php
namespace Neos\Flow\Tests\Unit\Reflection;

/*
 * This file is part of the Neos.Flow package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Doctrine\Common\Annotations\Reader;
use Neos\Flow\Reflection\Exception\ClassLoadingForReflectionFailedException;
use Neos\Flow\Reflection\ReflectionService;
use Neos\Flow\Tests\UnitTestCase;

/**
 * Testcase for the ReflectionService
 *
 */
class ReflectionServiceTest extends UnitTestCase
{
    /**
     * @var ReflectionService
     */
    protected $reflectionService;

    /**
     * @var Reader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockAnnotationReader;

    protected function setUp(): void
    {
        $this->reflectionService = $this->getAccessibleMock(ReflectionService::class, null);

        $this->mockAnnotationReader = $this->getMockBuilder('Doctrine\Common\Annotations\Reader')->disableOriginalConstructor()->getMock();
        $this->mockAnnotationReader->expects($this->any())->method('getClassAnnotations')->will($this->returnValue([]));
        $this->inject($this->reflectionService, 'annotationReader', $this->mockAnnotationReader);
    }

    /**
     * @test
     */
    public function reflectClassThrowsExceptionForNonExistingClasses()
    {
        $this->expectException(ClassLoadingForReflectionFailedException::class);
        $this->reflectionService->_call('reflectClass', 'Non\Existing\Class');
    }

    /**
     * @test
     */
    public function reflectClassThrowsExceptionForFilesWithNoClass()
    {
        $this->expectException(ClassLoadingForReflectionFailedException::class);
        $this->reflectionService->_call('reflectClass', Fixture\FileWithNoClass::class);
    }

    /**
     * @test
     */
    public function reflectClassThrowsExceptionForClassesWithNoMatchingFilename()
    {
        $this->expectException(ClassLoadingForReflectionFailedException::class);
        $this->reflectionService->_call('reflectClass', Fixture\ClassWithDifferentNameDifferent::class);
    }

    /**
     * @test
     */
    public function isTagIgnoredReturnsTrueForIgnoredTags()
    {
        $settings = ['reflection' => ['ignoredTags' => ['ignored' => true]]];
        $this->reflectionService->injectSettings($settings);

        $this->assertTrue($this->reflectionService->_call('isTagIgnored', 'ignored'));
    }

    /**
     * @test
     */
    public function isTagIgnoredReturnsFalseForTagsThatAreNotIgnored()
    {
        $settings = ['reflection' => ['ignoredTags' => ['notignored' => false]]];
        $this->reflectionService->injectSettings($settings);

        $this->assertFalse($this->reflectionService->_call('isTagIgnored', 'notignored'));
    }

    /**
     * @test
     */
    public function isTagIgnoredReturnsFalseForTagsThatAreNotConfigured()
    {
        $settings = ['reflection' => ['ignoredTags' => ['ignored' => true, 'notignored' => false]]];
        $this->reflectionService->injectSettings($settings);

        $this->assertFalse($this->reflectionService->_call('isTagIgnored', 'notconfigured'));
    }

    /**
     * @test
     */
    public function isTagIgnoredWorksWithOldConfiguration()
    {
        $settings = ['reflection' => ['ignoredTags' => ['ignored']]];
        $this->reflectionService->injectSettings($settings);

        $this->assertTrue($this->reflectionService->_call('isTagIgnored', 'ignored'));
        $this->assertFalse($this->reflectionService->_call('isTagIgnored', 'notignored'));
    }
}
