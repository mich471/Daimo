#### **Token**

This extension help other extensions to manage tokens

Supported tokens:
* Customer (id, email, additional data)

For adding new token please create implementation of `\Plumrocket\Token\Api\TypeInterface` and insert it into specific type pool, for example:
```xml
<type name="Plumrocket\Token\Api\CustomerTypePoolInterface">
    <arguments>
        <argument name="types" xsi:type="array">
            <item name="arar_auto_login" xsi:type="object">Plumrocket\AdvancedReviewAndReminder\Model\Token\AutoLoginType</item>
        </argument>
    </arguments>
</type>
```