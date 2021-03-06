<?php
namespace HtmlConversionTest\Model\Queue;

use Xmlps\UnitTest\ModelTest;
use Manager\Entity\Job;
use HtmlConversion\Model\Queue\Job\HtmlJob;

class HtmlJobTest extends ModelTest
{
    protected $document;
    protected $job;
    protected $user;

    protected $htmlJob;

    protected $testAsset = 'module/HtmlConversion/test/assets/document.xml';
    protected $testFile = '/tmp/UNITTEST_html_document.xml';

    /**
     * Initialize the test
     *
     * @return void
     */
    public function setUp() {
        parent::setUp();

        $this->htmlJob = new HtmlJob;
        $this->htmlJob->setServiceLocator($this->sm);

        $this->resetTestData();
    }

    /**
     * Test if the conversion works properly
     *
     * @return void
     */
    public function testConversion()
    {
        $this->assertSame($this->job->conversionStage, JOB_CONVERSION_STAGE_EPUB);
        $this->assertSame($this->document->conversionStage, JOB_CONVERSION_STAGE_XML_MERGE);
        $documentCount = count($this->job->documents);
        $this->htmlJob->process($this->job);
        $this->assertNotSame($this->job->status, JOB_STATUS_FAILED);
        $this->assertSame($documentCount + 1, count($this->job->documents));
        $this->assertSame($this->job->conversionStage, JOB_CONVERSION_STAGE_HTML);
    }

    /**
     * Create test data
     *
     * @return void
     */
    protected function createTestData() {
        @copy($this->testAsset, $this->testFile);

        // Create test user
        $this->user = $this->createTestUser();

        // Create test job
        $this->job = $this->createTestJob(
            array(
                'user' => $this->user,
                'conversionStage' => JOB_CONVERSION_STAGE_EPUB
            )
        );

        // Create test document
        $this->document = $this->createTestDocument(
            array(
                'job' => $this->job,
                'path' => $this->testFile,
                'conversionStage' => JOB_CONVERSION_STAGE_XML_MERGE
            )
        );
        $this->job->documents[] = $this->document;

        $this->getJobDAO()->save($this->job);
    }

    /**
     * Remove test data
     *
     * @return void
     */
    protected function cleanTestData()
    {
        $this->deleteTestUser();

        @unlink($this->testFile);
    }
}
