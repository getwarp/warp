<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\VersionControl\GitMergeConflictSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\LanguageConstructSpacingSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff;
use PhpCsFixer\Fixer\Alias\RandomApiMigrationFixer;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer;
use PhpCsFixer\Fixer\ArrayNotation\WhitespaceAfterCommaInArrayFixer;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer;
use PhpCsFixer\Fixer\Casing\MagicConstantCasingFixer;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\CastNotation\LowercaseCastFixer;
use PhpCsFixer\Fixer\CastNotation\ShortScalarCastFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer;
use PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleTraitInsertPerStatementFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer;
use PhpCsFixer\Fixer\ConstantNotation\NativeConstantInvocationFixer;
use PhpCsFixer\Fixer\ControlStructure\ElseifFixer;
use PhpCsFixer\Fixer\ControlStructure\NoBreakCommentFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededCurlyBracesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSpaceFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer;
use PhpCsFixer\Fixer\FunctionNotation\NullableTypeDeclarationForDefaultNullValueFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer;
use PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer;
use PhpCsFixer\Fixer\Import\NoLeadingImportSlashFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer;
use PhpCsFixer\Fixer\LanguageConstruct\ExplicitIndirectVariableFixer;
use PhpCsFixer\Fixer\LanguageConstruct\FunctionToConstantFixer;
use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;
use PhpCsFixer\Fixer\ListNotation\ListSyntaxFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer;
use PhpCsFixer\Fixer\NamespaceNotation\NoLeadingNamespaceWhitespaceFixer;
use PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\NewWithBracesFixer;
use PhpCsFixer\Fixer\Operator\StandardizeIncrementFixer;
use PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\TernaryToNullCoalescingFixer;
use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSingleLineVarSpacingFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer;
use PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer;
use PhpCsFixer\Fixer\ReturnNotation\ReturnAssignmentFixer;
use PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer;
use PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Semicolon\SpaceAfterSemicolonFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use PhpCsFixer\Fixer\StringNotation\ExplicitStringVariableFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\CompactNullableTypehintFixer;
use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use PhpCsFixer\Fixer\Whitespace\LineEndingFixer;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesAroundOffsetFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesInsideParenthesisFixer;
use PhpCsFixer\Fixer\Whitespace\NoTrailingWhitespaceFixer;
use PhpCsFixer\Fixer\Whitespace\NoWhitespaceInBlankLineFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use SlevomatCodingStandard\Sniffs\Classes\ParentCallSpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\TraitUseDeclarationSniff;
use SlevomatCodingStandard\Sniffs\Classes\TraitUseSpacingSniff;
use SlevomatCodingStandard\Sniffs\Commenting\DisallowCommentAfterCodeSniff;
use SlevomatCodingStandard\Sniffs\Commenting\EmptyCommentSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\RequireShortTernaryOperatorSniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedInheritedVariablePassedToClosureSniff;
use SlevomatCodingStandard\Sniffs\Operators\RequireCombinedAssignmentOperatorSniff;
use SlevomatCodingStandard\Sniffs\PHP\UselessParenthesesSniff;
use SlevomatCodingStandard\Sniffs\PHP\UselessSemicolonSniff;
use SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff;
use SlevomatCodingStandard\Sniffs\Variables\UselessVariableSniff;
use SlevomatCodingStandard\Sniffs\Whitespaces\DuplicateSpacesSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\CodingStandard\Fixer\Spacing\NewlineServiceDefinitionConfigFixer;
use Symplify\CodingStandard\Fixer\Spacing\StandaloneLinePromotedPropertyFixer;
use Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    // Arrays
    $services->set(ArrayOpenerAndCloserNewlineFixer::class);
    $services->set(ArrayIndentationFixer::class);
    $services->set(TrimArraySpacesFixer::class);
    $services->set(ArrayListItemNewlineFixer::class);
    $services->set(StandaloneLineInMultilineArrayFixer::class);
    $services->set(NoTrailingCommaInSinglelineArrayFixer::class);
    $services->set(ArraySyntaxFixer::class)
        ->call('configure', [
            [
                'syntax' => 'short',
            ],
        ]);
    $services->set(TrailingCommaInMultilineFixer::class)
        ->call('configure', [
            [
                'elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARRAYS],
            ],
        ]);

    // Comments
    $services->set(DisallowCommentAfterCodeSniff::class);
    $services->set(GitMergeConflictSniff::class);
    $services->set(EmptyCommentSniff::class);

    // Control structures
    $services->set(PhpUnitMethodCasingFixer::class);
    $services->set(FunctionToConstantFixer::class);
    $services->set(ExplicitStringVariableFixer::class);
    $services->set(ExplicitIndirectVariableFixer::class);
    $services->set(StandardizeIncrementFixer::class);
    $services->set(SelfAccessorFixer::class);
    $services->set(MagicConstantCasingFixer::class);
//    $services->set(AssignmentInConditionSniff::class);
    $services->set(NoUselessElseFixer::class);
    $services->set(SingleQuoteFixer::class);
    $services->set(YodaStyleFixer::class)
        ->call('configure', [
            [
                'equal' => true,
                'identical' => true,
                'less_and_greater' => true,
            ],
        ]);
    $services->set(OrderedClassElementsFixer::class);
    // TODO: check this sniff
    $services->set(TraitUseDeclarationSniff::class);

    // Namespaces
    $services->set(NoUnusedImportsFixer::class);
    $services->set(SingleBlankLineBeforeNamespaceFixer::class);
    $services->set(GlobalNamespaceImportFixer::class)
        ->call('configure', [
            [
                'import_classes' => false,
                'import_constants' => false,
                'import_functions' => false,
            ],
        ]);
    $services->set(NativeFunctionInvocationFixer::class)
        ->call('configure', [
            [
                'include' => ['@all'],
                'scope' => 'namespaced',
            ],
        ]);
    $services->set(NativeConstantInvocationFixer::class)
        ->call('configure', [
            [
                'scope' => 'namespaced',
            ],
        ]);

    // Spaces
    $services->set(StandaloneLinePromotedPropertyFixer::class);
    $services->set(NewlineServiceDefinitionConfigFixer::class);
    $services->set(MethodChainingIndentationFixer::class);
    $services->set(ClassAttributesSeparationFixer::class)
        ->call('configure', [
            [
                'elements' => [
                    'const' => 'one',
                    'property' => 'one',
                    'method' => 'one',
                ],
            ],
        ]);
    $services->set(SuperfluousWhitespaceSniff::class)
        ->property('ignoreBlankLines', false);
    $services->set(CastSpacesFixer::class)
        ->call('configure', [
            [
                'space' => 'none',
            ],
        ]);

    $services->set(SingleTraitInsertPerStatementFixer::class);
    $services->set(FunctionTypehintSpaceFixer::class);
    $services->set(PhpdocSingleLineVarSpacingFixer::class);
    $services->set(NoLeadingNamespaceWhitespaceFixer::class);
    $services->set(NoSpacesAroundOffsetFixer::class);
    $services->set(NoWhitespaceInBlankLineFixer::class);
    $services->set(SpaceAfterSemicolonFixer::class);
    $services->set(LanguageConstructSpacingSniff::class);
    // TODO: check this sniffs
    $services->set(ParentCallSpacingSniff::class);
    $services->set(DuplicateSpacesSniff::class);
    $services->set(TraitUseSpacingSniff::class)
        ->property('linesCountAfterLastUse', 1)
        ->property('linesCountAfterLastUseWhenLastInClass', 0)
        ->property('linesCountBeforeFirstUse', 0)
        ->property('linesCountBetweenUses', 0);

    // Strict
    $services->set(StrictComparisonFixer::class);
    $services->set(StrictParamFixer::class);
    $services->set(IsNullFixer::class);

    // PHP 7.0
    $services->set(TernaryToNullCoalescingFixer::class);
    $services->set(DeclareStrictTypesFixer::class);
    $services->set(BlankLineAfterStrictTypesFixer::class);
    $services->set(RandomApiMigrationFixer::class)
        ->call('configure', [
            [
                'replacements' => [
                    'mt_rand' => 'random_int',
                    'rand' => 'random_int',
                ],
            ],
        ]);

    // PHP 7.1
    $services->set(NullableTypeDeclarationForDefaultNullValueFixer::class);
    $services->set(CompactNullableTypehintFixer::class);
    $services->set(VoidReturnFixer::class);
    $services->set(ListSyntaxFixer::class)
        ->call('configure', [
            [
                'syntax' => 'short',
            ],
        ]);

    // Clean Code
    $services->set(ParamReturnAndVarTagMalformsFixer::class);
    $services->set(UnusedVariableSniff::class);
    $services->set(UselessVariableSniff::class);
    $services->set(UnusedInheritedVariablePassedToClosureSniff::class);
    $services->set(UselessSemicolonSniff::class);
    $services->set(UselessParenthesesSniff::class);
    $services->set(NoEmptyStatementFixer::class);
    $services->set(ProtectedToPrivateFixer::class);
    $services->set(NoUnneededControlParenthesesFixer::class);
    $services->set(NoUnneededCurlyBracesFixer::class);
    $services->set(ReturnAssignmentFixer::class);
    $services->set(RequireShortTernaryOperatorSniff::class);
    $services->set(RequireCombinedAssignmentOperatorSniff::class);

    // PSR-12
    $services->set(EncodingFixer::class);
    $services->set(FullOpeningTagFixer::class);
    $services->set(BlankLineAfterNamespaceFixer::class);
    $services->set(ConstantCaseFixer::class);
    $services->set(ElseifFixer::class);
    $services->set(FunctionDeclarationFixer::class);
    $services->set(IndentationTypeFixer::class);
    $services->set(LineEndingFixer::class);
    $services->set(LowercaseKeywordsFixer::class);
    $services->set(NoBreakCommentFixer::class);
    $services->set(NoClosingTagFixer::class);
    $services->set(NoSpacesAfterFunctionNameFixer::class);
    $services->set(NoSpacesInsideParenthesisFixer::class);
    $services->set(NoTrailingWhitespaceFixer::class);
    $services->set(NoTrailingWhitespaceInCommentFixer::class);
    $services->set(SingleBlankLineAtEofFixer::class);
    $services->set(SingleClassElementPerStatementFixer::class);
    $services->set(SingleLineAfterImportsFixer::class);
    $services->set(SwitchCaseSemicolonToColonFixer::class);
    $services->set(SwitchCaseSpaceFixer::class);
    $services->set(LowercaseCastFixer::class);
    $services->set(ShortScalarCastFixer::class);
    $services->set(BlankLineAfterOpeningTagFixer::class);
    $services->set(NoLeadingImportSlashFixer::class);
    $services->set(NewWithBracesFixer::class);
    $services->set(NoBlankLinesAfterClassOpeningFixer::class);
    $services->set(TernaryOperatorSpacesFixer::class);
    $services->set(UnaryOperatorSpacesFixer::class);
    $services->set(ReturnTypeDeclarationFixer::class);
    $services->set(NoSinglelineWhitespaceBeforeSemicolonsFixer::class);
    $services->set(NoWhitespaceBeforeCommaInArrayFixer::class);
    $services->set(WhitespaceAfterCommaInArrayFixer::class);
    $services->set(ClassDefinitionFixer::class)
        ->call('configure', [
            [
                'single_item_single_line' => true,
            ],
        ]);
    $services->set(VisibilityRequiredFixer::class)
        ->call('configure', [
            [
                'elements' => ['const', 'property', 'method'],
            ],
        ]);
    $services->set(MethodArgumentSpaceFixer::class)
        ->call('configure', [
            [
                'on_multiline' => 'ensure_fully_multiline',
            ],
        ]);
    $services->set(OrderedImportsFixer::class)
        ->call('configure', [
            [
                'imports_order' => ['class', 'function', 'const'],
            ],
        ]);
    $services->set(DeclareEqualNormalizeFixer::class)
        ->call('configure', [
            [
                'space' => 'none',
            ],
        ]);
    $services->set(BracesFixer::class)
        ->call('configure', [
            [
                'allow_single_line_closure' => false,
                'position_after_functions_and_oop_constructs' => 'next',
                'position_after_control_structures' => 'same',
                'position_after_anonymous_constructs' => 'same',
            ],
        ]);
    $services->set(BinaryOperatorSpacesFixer::class)
        ->call('configure', [
            [
                'operators' => [
                    '=>' => 'single_space',
                    '=' => 'single_space',
                    '|' => 'no_space',
                    '&' => 'no_space',
                ],
            ],
        ]);
    $services->set(ConcatSpaceFixer::class)
        ->call('configure', [
            [
                'spacing' => 'one',
            ],
        ]);
    $services->set(LineLengthFixer::class)
        ->call('configure', [
            [
                LineLengthFixer::LINE_LENGTH => 120,
                LineLengthFixer::INLINE_SHORT_LINES => false,
            ],
        ]);
};
