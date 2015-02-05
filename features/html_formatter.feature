Feature: HTML formatter support
  In order to see html report
  As an developer
  I need to use html formatter

  Scenario: Printing html output to file
    Given there is config file with:
    """
    default:
      extensions:
        Behat\Reporter\ReporterExtension: ~
    """
    And there is feature context
    And there is feature "apples_story" with:
    """
    Feature: Apples story
      In order to eat apple
      As a little kid
      I need to have an apple in my pocket

      Background:
        Given I have 3 apples

      Scenario: I'm little hungry
        When I ate 1 apple
        Then I should have 2 apples

      Scenario: Found more apples
        When I found 5 apples
        Then I should have 8 apples

      Scenario: Found more apples
        When I found 2 apples
        Then I should have 5 apples

      Scenario Outline: Other situations
        When I ate <ate> apples
        And I found <found> apples
        Then I should have <result> apples

        Examples:
          | ate | found | result |
          | 3   | 1     | 1      |
          | 0   | 4     | 7      |
          | 2   | 2     | 3      |
    """
    When I run behat with "-f html -o report.html"
    Then it should pass
    And the report.html file should exists
    # scrap html content with Symfony
