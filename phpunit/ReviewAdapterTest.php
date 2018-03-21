<?php
namespace phpunit\Wec\Review;

use Wec\Review\ReviewAdapter;
use Wec\Review\Dto\ReviewerDto;

class ReviewAdapterTest extends AdapterTestBase
{
    public function testApprove(): void
    {
        $this->initParamIndex();
        $dstId = 'fakeDstId';
        $employeeId = 'fakeEmployeeId';
        $message = 'fakeMessage';
        $type = 'order';

        $reviewAdapter = new ReviewAdapter($type, $this->getDmgStub());
        $reviewAdapter->approve($employeeId, $dstId, $message);

        $executed = $this->getCnn()->executed();
        $stmt = $executed[0];
        
        $sql = $stmt->sql();
        $vals = $stmt->vals();
        
        $this->assertEquals(
            'INSERT INTO order_review (reviewId, orderId, employeeId, message, result, created) VALUES (:k1, :k2, :k3, :k4, :k5, :k6)',
            $sql
        );

        unset($vals[':k1']);
        unset($vals[':k6']);
        $this->assertEquals(
            [':k2' => $dstId, ':k3' => $employeeId, ':k4' => $message, ':k5' => 'approved'],
            $vals
        );
    }

    public function testReject(): void
    {
        $this->initParamIndex();
        $dstId = 'fakeDstId';
        $employeeId = 'fakeEmployeeId';
        $message = 'fakeMessage';
        $type = 'order';

        $reviewAdapter = new ReviewAdapter($type, $this->getDmgStub());
        $reviewAdapter->reject($employeeId, $dstId, $message);

        $executed = $this->getCnn()->executed();
        $stmt = $executed[0];
        
        $sql = $stmt->sql();
        $vals = $stmt->vals();

        $this->assertEquals(
            'INSERT INTO order_review (reviewId, orderId, employeeId, message, result, created) VALUES (:k1, :k2, :k3, :k4, :k5, :k6)',
            $sql
        );

        unset($vals[':k1']);
        unset($vals[':k6']);
        $this->assertEquals(
            [':k2' => $dstId, ':k3' => $employeeId, ':k4' => $message, ':k5' => 'rejected'],
            $vals
        );
    }

    public function testFetchReview(): void
    {
        $this->initParamIndex();
        
        $reviewId = 'fakeReviewId';

        $reviewAdapter = new ReviewAdapter('order', $this->getDmgStub());
        $reviewAdapter->fetchReview($reviewId);

        $executed = $this->getCnn()->executed();
        $stmt = $executed[0];
        $sql = $stmt->sql();
        $vals = $stmt->vals();

        $this->assertEquals(
            'SELECT * FROM order_review WHERE reviewId = :k1 LIMIT 10',
            $sql
        );

        $this->assertEquals(
            [':k1' => $reviewId],
            $vals
        );
    }

    public function testListReview(): void
    {
        $this->initParamIndex();
        
        $dstId = 'fakeOrderId';

        $reviewAdapter = new ReviewAdapter('order', $this->getDmgStub());
        $reviewList = $reviewAdapter->listReview($dstId);
        $reviewList->rewind();

        $executed = $this->getCnn()->executed();
        $stmt = $executed[0];
        $sql = $stmt->sql();
        $vals = $stmt->vals();

        $this->assertEquals(
            'SELECT * FROM order_review WHERE orderId = :k1 ORDER BY created ASC LIMIT 10',
            $sql
        );

        $this->assertEquals(
            [':k1' => $dstId],
            $vals
        );
    }
}

// https://phpunit.de/manual/current/en/test-doubles.html
