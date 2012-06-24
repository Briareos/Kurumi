<?php

namespace App\UserBundle\Form\Choice\UserList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Doctrine\ORM\EntityManager;

class UserList implements ChoiceListInterface {

    private $em;
    private $criteria;

    public function __construct(EntityManager $em, $respositoryName, $criteria) {
        $this->em = $em;
        $this->criteria = $criteria;
    }
    /**
     * Returns the list of choices
     *
     * @return array The choices with their indices as keys.
     */
    function getChoices()
    {
    }

    /**
     * Returns the values for the choices
     *
     * @return array The values with the corresponding choice indices as keys.
     */
    function getValues()
    {
        // TODO: Implement getValues() method.
    }

    /**
     * Returns the choice views of the preferred choices as nested array with
     * the choice groups as top-level keys.
     *
     * Example:
     *
     * <source>
     * array(
     *     'Group 1' => array(
     *         10 => ChoiceView object,
     *         20 => ChoiceView object,
     *     ),
     *     'Group 2' => array(
     *         30 => ChoiceView object,
     *     ),
     * )
     * </source>
     *
     * @return array A nested array containing the views with the corresponding
     *               choice indices as keys on the lowest levels and the choice
     *               group names in the keys of the higher levels.
     */
    function getPreferredViews()
    {
        // TODO: Implement getPreferredViews() method.
    }

    /**
     * Returns the choice views of the choices that are not preferred as nested
     * array with the choice groups as top-level keys.
     *
     * Example:
     *
     * <source>
     * array(
     *     'Group 1' => array(
     *         10 => ChoiceView object,
     *         20 => ChoiceView object,
     *     ),
     *     'Group 2' => array(
     *         30 => ChoiceView object,
     *     ),
     * )
     * </source>
     *
     * @return array A nested array containing the views with the corresponding
     *               choice indices as keys on the lowest levels and the choice
     *               group names in the keys of the higher levels.
     *
     * @see getPreferredValues
     */
    function getRemainingViews()
    {
        // TODO: Implement getRemainingViews() method.
    }

    /**
     * Returns the choices corresponding to the given values.
     *
     * @param array $values An array of choice values. Not existing values in
     *                      this array are ignored.
     *
     * @return array An array of choices with ascending, 0-based numeric keys
     */
    function getChoicesForValues(array $values)
    {
        // TODO: Implement getChoicesForValues() method.
    }

    /**
     * Returns the values corresponding to the given choices.
     *
     * @param array $choices An array of choices. Not existing choices in this
     *                       array are ignored.
     *
     * @return array An array of choice values with ascending, 0-based numeric
     *               keys
     */
    function getValuesForChoices(array $choices)
    {
        // TODO: Implement getValuesForChoices() method.
    }

    /**
     * Returns the indices corresponding to the given choices.
     *
     * @param array $choices An array of choices. Not existing choices in this
     *                       array are ignored.
     *
     * @return array An array of indices with ascending, 0-based numeric keys
     */
    function getIndicesForChoices(array $choices)
    {
        // TODO: Implement getIndicesForChoices() method.
    }

    /**
     * Returns the indices corresponding to the given values.
     *
     * @param array $values An array of choice values. Not existing values in
     *                      this array are ignored.
     *
     * @return array An array of indices with ascending, 0-based numeric keys
     */
    function getIndicesForValues(array $values)
    {
        // TODO: Implement getIndicesForValues() method.
    }


}