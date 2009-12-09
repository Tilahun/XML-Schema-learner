<?php
/**
 * Schema learning
 *
 * This file is part of SchemaLearner.
 *
 * SchemaLearner is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * SchemaLearner is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with SchemaLearner; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Class representing a schema.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class slSchema
{
    /**
     * Array of types
     *
     * Contains a list of all found elements / types with their context
     * information.
     *
     * The slSchemaElement contains information about the elements simple,
     * type, attriubutes and its regular expression.
     * 
     * @var array
     */
    protected $types = array();

    /**
     * Types of found root elements
     * 
     * @var array
     */
    protected $rootElements = array();

    /**
     * Construct new schema class
     * 
     * @return void
     */
    public function __construct()
    {
        $this->types        = array();
        $this->rootElements = array();
    }

    /**
     * Inference type from DOMElement
     * 
     * @param DOMElement $element 
     * @return void
     */
    abstract protected function inferenceType( DOMElement $element );

    /**
     * Learn XML file
     *
     * Learn the automaton from an XML file
     * 
     * @param string $file 
     * @return void
     */
    public function learnFile( $file )
    {
        $doc = new DOMDocument();
        $doc->load( $file );
        $this->traverse( $doc );
    }

    /**
     * Get regular expressions for learned schema
     *
     * Get an array of type -> regular expression associations for the learned 
     * schema.
     * 
     * @return array(slSchemaElement)
     */
    public function getTypes()
    {
        // Ensure the regular expressions in all types are up to date
        foreach ( $this->types as $type => $element )
        {
            $element->regularExpression = $this->convertRegularExpression( $element->automaton );
        };

        return $this->types;
    }

    /**
     * Return types found as root elements
     *
     * Returns an array with the string representations of the types, which
     * have been found as root elements in the provided schemas.
     * 
     * @return void
     */
    public function getRootElements()
    {
        return array_keys( $this->rootElements );
    }

    /**
     * Lear Automaton for element
     * 
     * @param DOMElement $element 
     * @param array $children 
     * @return void
     */
    protected function learnAutomaton( DOMElement $element, array $children )
    {
        $type = $this->getType( $this->inferenceType( $element ) );

        $elements = array();
        foreach ( $children as $child )
        {
            $elements[] = $this->inferenceType( $child );
        }

        // @todo: Update the element from found contents, etc.

        $type->automaton->learn( $elements );
    }

    /**
     * Return element representation for the given type
     *
     * Return the element representation object for the provided type. If non
     * exists yet a new blank one will be created.
     *
     * The slSchemaElement contains information about the elements simple,
     * type, attriubutes and its regular expression.
     * 
     * @param mixed $type 
     * @return void
     */
    protected function getType( $type )
    {
        if ( isset( $this->types[$type] ) )
        {
            return $this->types[$type];
        }

        return $this->types[$type] = new slSchemaElement( $type );
    }

    /**
     * Traverse XML tree
     *
     * Traverses the XML tree and calls the learnAutomaton() method for each 
     * element, with its child element nodes.
     * 
     * @param DOMNode $root 
     * @return void
     */
    protected function traverse( DOMNode $root )
    {
        if ( $root->parentNode instanceof DOMDocument )
        {
            $this->rootElements[$this->inferenceType( $root )] = true;
        }

        $elements = array();
        foreach ( $root->childNodes as $node )
        {
            if ( $node->nodeType !== XML_ELEMENT_NODE )
            {
                continue;
            }

            $elements[] = $node;
            $this->traverse( $node );
        }

        if ( ( $root->nodeType === XML_ELEMENT_NODE ) &&
             count( $elements ) )
        {
            $this->learnAutomaton( $root, $elements );
        }
    }

    /**
     * Convert automaton to regular expression
     * 
     * @param slAutomaton $automaton 
     * @return slRegularExpression
     */
    protected function convertRegularExpression( $automaton )
    {
        // Convert automatons
        $converter = new slSoreConverter();
        if ( ( $expression = $converter->convertAutomaton( $automaton ) ) !== false )
        {
            return $expression;
        }

        $converter = new slChareConverter();
        return $converter->convertAutomaton( $automaton );
    }
}
 
