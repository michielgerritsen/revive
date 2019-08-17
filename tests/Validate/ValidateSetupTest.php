<?php
/**
 *    ______            __             __
 *   / ____/___  ____  / /__________  / /
 *  / /   / __ \/ __ \/ __/ ___/ __ \/ /
 * / /___/ /_/ / / / / /_/ /  / /_/ / /
 * \______________/_/\__/_/   \____/_/
 *    /   |  / / /_
 *   / /| | / / __/
 *  / ___ |/ / /_
 * /_/ _|||_/\__/ __     __
 *    / __ \___  / /__  / /____
 *   / / / / _ \/ / _ \/ __/ _ \
 *  / /_/ /  __/ /  __/ /_/  __/
 * /_____/\___/_/\___/\__/\___/
 *
 */

namespace MichielGerritsen\Revive\Test\Validate;

use MichielGerritsen\Revive\Validate\ValidateSetup;
use MichielGerritsen\Revive\Validate\Validators\ValidatorContract;
use PHPUnit\Framework\TestCase;

class ValidateSetupTest extends TestCase
{
    public function testReturnsFalseIfAValidatorFails()
    {
        $validator1 = $this->createMock(ValidatorContract::class);
        $validator1->method('validate')->willReturn(true);

        $validator2 = $this->createMock(ValidatorContract::class);
        $validator2->method('validate')->willReturn(false);

        $validator3 = $this->createMock(ValidatorContract::class);
        $validator3->method('validate')->willReturn(true);

        container()->flush();
        $instance = container()->make(ValidateSetup::class, [
            'validators' => [
                $validator1,
                $validator2,
                $validator3,
            ]
        ]);

        $this->assertFalse($instance->validate());
    }

    public function testSucceedsIfAllValidatorAreValid()
    {
        $validator = $this->createMock(ValidatorContract::class);
        $validator->method('validate')->willReturn(true);

        container()->flush();
        $instance = container()->make(ValidateSetup::class, [
            'validators' => [$validator]
        ]);

        $this->assertTrue($instance->validate());
    }

    public function testCollectsErrors()
    {
        $validator1 = $this->createMock(ValidatorContract::class);
        $validator1->method('validate')->willReturn(false);
        $validator1->method('getErrors')->willReturn(['error 1']);
        $validator1->method('shouldContinue')->willReturn(true);

        $validator2 = $this->createMock(ValidatorContract::class);
        $validator2->method('validate')->willReturn(false);
        $validator2->method('getErrors')->willReturn(['error 2']);
        $validator2->method('shouldContinue')->willReturn(true);

        container()->flush();
        $instance = container()->make(ValidateSetup::class, [
            'validators' => [$validator1, $validator2]
        ]);

        $instance->validate();

        $this->assertCount(2, $instance->getErrors());
        $this->assertTrue(in_array('error 1', $instance->getErrors()));
        $this->assertTrue(in_array('error 2', $instance->getErrors()));
    }

    public function testSkipTheRestOfTheValidatorsIfNeeded()
    {
        $validator1 = $this->createMock(ValidatorContract::class);
        $validator1->expects($this->once())->method('validate')->willReturn(false);
        $validator1->method('shouldContinue')->willReturn(false);

        $validator2 = $this->createMock(ValidatorContract::class);
        $validator2->expects($this->never())->method('validate');

        container()->flush();
        $instance = container()->make(ValidateSetup::class, [
            'validators' => [$validator1, $validator2]
        ]);

        $instance->validate();
    }
}
