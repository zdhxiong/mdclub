/**
 * 代码高亮
 * @param html
 */
export declare function richText(html: string): (element: HTMLElement) => void;

/**
 * 移除字符串中的 HTML 标签
 * @param html
 * @param length 截取的字符长度
 */
export declare function summaryText(html: string, length: number): (element: HTMLElement) => void;

/**
 * 移除字符串中的 HTML 标签
 * @param html
 */
export declare function summaryText(html: string): (element: HTMLElement) => void;

/**
 * 纯文本，把换行符转换为 <br/>
 * @param text
 */
export declare function plainText(text: string): (element: HTMLElement) => void;
