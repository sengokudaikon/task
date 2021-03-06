<?php

namespace App\ArgumentResolver;

use App\DTO\CreateNewsData;
use App\Exception\ApiValidationException;
use App\Validation\CreateNewsDataValidator;
use App\VO\ApiErrorCode;
use DateTime;
use Exception;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class CreateNewsDataResolver implements ArgumentValueResolverInterface
{
    /**
     * @var CreateNewsDataValidator
     */
    private $validator;

    /**
     * @param CreateNewsDataValidator $validator
     */
    public function __construct(CreateNewsDataValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return CreateNewsData::class === $argument->getType();
    }

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return Generator
     *
     * @throws Exception
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        $params = json_decode($request->getContent(), true);
        $title = $params['title'];
        $slug = $params['slug'];
        $description = $params['description'];
        $shortDescription = $params['short_description'];
        $isActive = $params['is_active'];
        $isHidden = $params['is_hidden'];

        $errors = $this->validator->validate(
            [
                'title' => $title,
                'slug' => $slug,
                'description' => $description,
                'short_description' => $shortDescription
            ]
        );

        if (!empty($errors)) {
            throw new ApiValidationException($errors, new ApiErrorCode(ApiErrorCode::VALIDATION_ERROR));
        }

        yield new CreateNewsData(
            $title,
            $slug,
            $description,
            $shortDescription,
            new DateTime(),
            $isActive,
            $isHidden
        );
    }
}
