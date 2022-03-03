<?php declare(strict_types=1);

namespace Soyhuce\Testing\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Assert;
use function count;
use function is_int;

/**
 * @see https://gist.github.com/colindecarlo/9ba9bd6524127fee7580ae66c6d4709d
 */
class TestValidationResult
{
    private const JSON_OPTIONS = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION;

    public function __construct(
        private Validator $validator,
        private ?ValidationException $failed = null,
    ) {
    }

    public function assertPasses(): self
    {
        Assert::assertNull(
            $this->failed,
            sprintf(
                "Validation of the payload:\n%s\ndid not pass validation rules\n%s\n",
                json_encode($this->getData(), self::JSON_OPTIONS),
                json_encode($this->getValidationMessages(), self::JSON_OPTIONS)
            )
        );

        return $this;
    }

    /**
     * @param array<array-key, string> $errors
     */
    public function assertFails(array $errors = []): self
    {
        Assert::assertNotNull(
            $this->failed,
            sprintf(
                "Validation of the payload:\n%s\npassed validation rules\n",
                json_encode($this->getData(), self::JSON_OPTIONS),
            )
        );

        if (count($errors) === 0) {
            return $this;
        }

        $validationMessages = $this->getValidationMessages();

        $errorMessage = 'Request has the following json validation errors:' . PHP_EOL . PHP_EOL .
            json_encode($validationMessages, self::JSON_OPTIONS) . PHP_EOL;

        foreach ($errors as $key => $value) {
            Assert::assertArrayHasKey(
                is_int($key) ? $value : $key,
                $validationMessages,
                <<<EOT
                Failed to find a validation error in the response for key: '{$value}'
                
                {$errorMessage}
                EOT
            );

            if (!is_int($key)) {
                $hasError = false;

                foreach (Arr::wrap($validationMessages[$key]) as $jsonErrorMessage) {
                    if (Str::contains($jsonErrorMessage, $value)) {
                        $hasError = true;

                        break;
                    }
                }

                if (!$hasError) {
                    Assert::fail(
                        <<<EOT
                        Failed to find a validation error in the response for key and message: '{$key}' => '{$value}'
                        
                        {$errorMessage} 
                        EOT
                    );
                }
            }
        }

        return $this;
    }

    /**
     * @return array<array-key, mixed>
     */
    private function getData(): array
    {
        if (!method_exists($this->validator, 'getData')) {
            return [];
        }

        return $this->validator->getData();
    }

    /**
     * @return Collection<string, array<string>>
     */
    private function getValidationMessages(): Collection
    {
        return new Collection($this->failed?->validator->errors()->getMessages());
    }
}
