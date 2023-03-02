Plumrocket GDPR Extension for M2


#### Frontend checkbox repository:

`view/frontend/web/js/model/consent/checkbox/storage-service.js`


## Integration's guides

### Consent Checkboxes

For render checkboxes of specific location ew recommend use

`view/frontend/templates/x-init/location_checkbox_list.phtml`

##### How to validate if client check checkbox on my custom form?

Extension has observer that automatically check this for you.
It watches request for param "prgdpr_location", see more here: [Observer\ValidateConsentsObserver](/Observer/ValidateConsentsObserver.php)

##### How to send specific json response when client didn't check checkbox? 

Create virtual type from  [Model\Consent\Validation\JsonResponseStrategy](Model/Consent/Validation/JsonResponseStrategy.php) and pass your own "formatResponseData" 
```xml
<config>
    <virtualType name="dataPrivacyResponseStrategy" type="Plumrocket\GDPR\Model\Consent\Validation\JsonResponseStrategy">
        <arguments>
            <argument name="formatResponseData" xsi:type="object">Plumrocket\Newsletterpopup\Model\DataPrivacy\NotAgreedResponseDataFormat</argument>
        </arguments>
    </virtualType>

    <type name="Plumrocket\GDPR\Observer\ValidateConsentsObserver">
        <arguments>
            <argument name="notAgreedResponseStrategies" xsi:type="array">
                <item name="prnewsletterpopup_index_subscribe" xsi:type="object">dataPrivacyResponseStrategy</item>
            </argument>
        </arguments>
    </type>
</config>
```

### Consent Locations

##### How to add my own location? 

There's possibility to add "consent location" via di.xml

This location will install at "recurring data" step of setup:upgrade

```xml
<type name="Plumrocket\GDPR\Api\ConsentLocationRegistryInterface">
    <arguments>
        <argument name="list" xsi:type="array">
            <!-- Name is Location key -->
            <item name="test" xsi:type="array">
                <!-- Required -->
                <item name="name" xsi:type="string">Test location</item>
                <!-- Optional, default value - 2. (0 - magento, 1 - plumrocket, 2 - custom) -->
                <item name="type" xsi:type="number">1</item>
                <!-- Optional, default value - ''. -->
                <item name="description" xsi:type="string">Test recurring functionality</item>
                <!-- Optional, default value - true. -->
                <item name="visible" xsi:type="boolean">true</item>
            </item>
        </argument>
    </arguments>
</type>
```
