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
 * Class representing a DTD schema.
 *
 * DTD schemas can only learn each element name as a single type, so that this 
 * schema uses the simple element name based type inferencer.
 *
 * @todo:
 *      The elements will be merged in thy type merger, so that multiple elements 
 *      may refer to the same type.
 *
 *      It will still not be possible to backtrack the locality of a type, 
 *      because a type does not know the elements it occurs in, and the 
 *      elements do not know the regular expressions they occur in.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slXsdSchema extends slSchema
{
    /**
     * Get schema dependent simple type inferencer
     * 
     * @return slSimpleTypeInferencer
     */
    protected function getSimpleInferencer()
    {
        return new slPcdataSimpleTypeInferencer();
    }
}

